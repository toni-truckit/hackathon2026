data "aws_caller_identity" "current" {}

data "aws_region" "current" {}

data "aws_ssm_parameter" "ami" {
  name = "/aws/service/ami-amazon-linux-latest/al2023-ami-kernel-default-x86_64"
}

resource "aws_ecr_repository" "this" {
  name                 = "truckit-mcp"
  image_tag_mutability = "MUTABLE"

  image_scanning_configuration {
    scan_on_push = true
  }
}

resource "aws_ecr_lifecycle_policy" "this" {
  repository = aws_ecr_repository.this.name

  policy = jsonencode({
    rules = [{
      rulePriority = 1
      description  = "Keep last 10 images"
      selection = {
        tagStatus   = "any"
        countType   = "imageCountMoreThan"
        countNumber = 10
      }
      action = { type = "expire" }
    }]
  })
}

resource "aws_cloudwatch_log_group" "app" {
  name              = "/truckit-mcp/${var.env}/app"
  retention_in_days = var.log_retention_days
}

resource "aws_cloudwatch_log_group" "nginx" {
  name              = "/truckit-mcp/${var.env}/nginx"
  retention_in_days = var.log_retention_days
}

data "aws_iam_policy_document" "ec2_assume" {
  statement {
    actions = ["sts:AssumeRole"]
    principals {
      type        = "Service"
      identifiers = ["ec2.amazonaws.com"]
    }
  }
}

resource "aws_iam_role" "ec2" {
  name               = "${var.name}-ec2"
  assume_role_policy = data.aws_iam_policy_document.ec2_assume.json
}

data "aws_iam_policy_document" "ec2" {
  statement {
    sid    = "EcrAuth"
    effect = "Allow"
    actions = [
      "ecr:GetAuthorizationToken",
    ]
    resources = ["*"]
  }

  statement {
    sid    = "EcrPull"
    effect = "Allow"
    actions = [
      "ecr:BatchCheckLayerAvailability",
      "ecr:GetDownloadUrlForLayer",
      "ecr:BatchGetImage",
    ]
    resources = [aws_ecr_repository.this.arn]
  }

  statement {
    sid       = "SecretsRead"
    effect    = "Allow"
    actions   = ["secretsmanager:GetSecretValue"]
    resources = var.secret_arns
  }

  statement {
    sid       = "SsmRead"
    effect    = "Allow"
    actions   = ["ssm:GetParameter", "ssm:GetParameters"]
    resources = var.parameter_arns
  }

  statement {
    sid    = "PhotosBucket"
    effect = "Allow"
    actions = [
      "s3:GetObject",
      "s3:PutObject",
      "s3:DeleteObject",
      "s3:ListBucket",
    ]
    resources = [
      var.photos_bucket_arn,
      "${var.photos_bucket_arn}/*",
    ]
  }

  statement {
    sid    = "Logs"
    effect = "Allow"
    actions = [
      "logs:CreateLogGroup",
      "logs:CreateLogStream",
      "logs:PutLogEvents",
      "logs:DescribeLogStreams",
    ]
    resources = [
      aws_cloudwatch_log_group.app.arn,
      "${aws_cloudwatch_log_group.app.arn}:*",
      aws_cloudwatch_log_group.nginx.arn,
      "${aws_cloudwatch_log_group.nginx.arn}:*",
    ]
  }
}

resource "aws_iam_role_policy" "ec2" {
  name   = "${var.name}-ec2"
  role   = aws_iam_role.ec2.id
  policy = data.aws_iam_policy_document.ec2.json
}

resource "aws_iam_role_policy_attachment" "ssm" {
  role       = aws_iam_role.ec2.name
  policy_arn = "arn:aws:iam::aws:policy/AmazonSSMManagedInstanceCore"
}

resource "aws_iam_instance_profile" "ec2" {
  name = "${var.name}-ec2"
  role = aws_iam_role.ec2.name
}

locals {
  ecr_registry = "${data.aws_caller_identity.current.account_id}.dkr.ecr.${data.aws_region.current.name}.amazonaws.com"
  ecr_repo_url = "${local.ecr_registry}/${aws_ecr_repository.this.name}"

  user_data = templatefile("${path.module}/templates/user_data.sh.tftpl", {
    deploy_script = templatefile("${path.module}/templates/deploy.sh.tftpl", {
      region          = data.aws_region.current.name
      image_tag_param = var.image_tag_param
      ecr_registry    = local.ecr_registry
      ecr_repo_url    = local.ecr_repo_url
    })
    render_env = templatefile("${path.module}/templates/render_env.py.tftpl", {
      region                      = data.aws_region.current.name
      env                         = var.env
      app_key_secret_arn          = var.app_key_secret_arn
      passport_private_secret_arn = var.passport_private_secret_arn
      passport_public_secret_arn  = var.passport_public_secret_arn
      truckit_secret_arn          = var.truckit_secret_arn
      ml_pricing_secret_arn       = var.ml_pricing_secret_arn
      llm_secret_arn              = var.llm_secret_arn
      db_secret_arn               = var.db_secret_arn
      redis_secret_arn            = var.redis_secret_arn
      app_url_param               = var.app_url_param
      oauth_redirects_param       = var.oauth_redirects_param
      feature_flags_param         = var.feature_flags_param
    })
    compose_ec2 = templatefile("${path.module}/templates/docker-compose.ec2.yml.tftpl", {
      region        = data.aws_region.current.name
      log_group_app = aws_cloudwatch_log_group.app.name
    })
    compose_placeholder = templatefile("${path.module}/templates/docker-compose.placeholder.yml.tftpl", {
      region          = data.aws_region.current.name
      log_group_nginx = aws_cloudwatch_log_group.nginx.name
    })
    nginx_conf = file("${path.module}/templates/placeholder-nginx.conf")
  })
}

resource "aws_launch_template" "this" {
  name_prefix   = "${var.name}-"
  image_id      = data.aws_ssm_parameter.ami.value
  instance_type = var.instance_type

  iam_instance_profile {
    name = aws_iam_instance_profile.ec2.name
  }

  network_interfaces {
    associate_public_ip_address = false
    security_groups             = [var.app_sg_id]
  }

  block_device_mappings {
    device_name = "/dev/xvda"

    ebs {
      volume_size           = 30
      volume_type           = "gp3"
      encrypted             = true
      delete_on_termination = true
    }
  }

  metadata_options {
    http_endpoint               = "enabled"
    http_tokens                 = "required"
    http_put_response_hop_limit = 1
  }

  user_data = base64encode(local.user_data)

  tag_specifications {
    resource_type = "instance"
    tags          = var.tags
  }

  lifecycle {
    create_before_destroy = true
  }
}

resource "aws_autoscaling_group" "this" {
  name                      = var.asg_name
  vpc_zone_identifier       = var.app_subnet_ids
  desired_capacity          = 1
  min_size                  = 1
  max_size                  = 1
  health_check_type         = "ELB"
  health_check_grace_period = 300
  target_group_arns         = [var.target_group_arn]

  launch_template {
    id      = aws_launch_template.this.id
    version = "$Latest"
  }

  tag {
    key                 = "Name"
    value               = var.name
    propagate_at_launch = true
  }

  dynamic "tag" {
    for_each = var.tags

    content {
      key                 = tag.key
      value               = tag.value
      propagate_at_launch = true
    }
  }

  lifecycle {
    ignore_changes = [desired_capacity]
  }
}
