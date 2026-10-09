output "ecr_repository_url" {
  value = local.ecr_repo_url
}

output "ecr_repository_arn" {
  value = aws_ecr_repository.this.arn
}

output "asg_name" {
  value = aws_autoscaling_group.this.name
}

output "log_group_app" {
  value = aws_cloudwatch_log_group.app.name
}

output "log_group_nginx" {
  value = aws_cloudwatch_log_group.nginx.name
}

output "log_group_arns" {
  value = [
    aws_cloudwatch_log_group.app.arn,
    "${aws_cloudwatch_log_group.app.arn}:*",
    aws_cloudwatch_log_group.nginx.arn,
    "${aws_cloudwatch_log_group.nginx.arn}:*",
  ]
}
