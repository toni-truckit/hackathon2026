data "aws_caller_identity" "current" {}
data "aws_region" "current" {}

locals {
  asg_name = "${var.name}-asg"

  ssm_parameter_arn_prefix = "arn:aws:ssm:${data.aws_region.current.name}:${data.aws_caller_identity.current.account_id}:parameter"

  ssm_parameter_arns = [
    "${local.ssm_parameter_arn_prefix}${module.secrets.app_url_parameter_name}",
    "${local.ssm_parameter_arn_prefix}${module.secrets.image_tag_parameter_name}",
    "${local.ssm_parameter_arn_prefix}${module.secrets.oauth_redirect_uris_parameter_name}",
    "${local.ssm_parameter_arn_prefix}${module.secrets.feature_flags_parameter_name}",
  ]
}

module "network" {
  source   = "../../modules/network"
  name     = var.name
  vpc_cidr = var.vpc_cidr
}

module "storage" {
  source = "../../modules/storage"
  name   = var.name
}

module "secrets" {
  source   = "../../modules/secrets"
  env      = var.env
  hostname = var.hostname
}

module "data" {
  source = "../../modules/data"

  name                     = var.name
  env                      = var.env
  private_subnet_ids       = module.network.private_subnet_ids
  rds_sg_id                = module.network.rds_sg_id
  redis_sg_id              = module.network.redis_sg_id
  db_instance_class        = var.db_instance_class
  db_backup_retention_days = 1
  redis_node_type          = var.redis_node_type
  redis_engine_version     = var.redis_engine_version
}

module "edge" {
  source = "../../modules/edge"

  name                    = var.name
  hostname                = var.hostname
  hosted_zone_name        = var.hosted_zone_name
  vpc_id                  = module.network.vpc_id
  public_subnet_ids       = module.network.public_subnet_ids
  alb_sg_id               = module.network.alb_sg_id
  logs_bucket_name        = module.storage.logs_bucket_name
  alb_logs_prefix         = module.storage.alb_logs_prefix
  waf_mcp_rate_limit      = var.waf_mcp_rate_limit
  waf_oauth_rate_limit    = var.waf_oauth_rate_limit
  waf_catchall_rate_limit = var.waf_catchall_rate_limit
}

module "compute" {
  source = "../../modules/compute"

  name              = var.name
  env               = var.env
  asg_name          = local.asg_name
  app_subnet_ids    = [module.network.private_subnet_ids[0]]
  app_sg_id         = module.network.app_sg_id
  target_group_arn  = module.edge.target_group_arn
  instance_type     = var.instance_type
  photos_bucket_arn = module.storage.photos_bucket_arn

  secret_arns = concat(
    values(module.secrets.secret_arns),
    [module.data.db_secret_arn, module.data.redis_secret_arn],
  )
  parameter_arns = local.ssm_parameter_arns

  app_key_secret_arn          = module.secrets.secret_arns["app-key"]
  passport_private_secret_arn = module.secrets.secret_arns["passport-private-key"]
  passport_public_secret_arn  = module.secrets.secret_arns["passport-public-key"]
  truckit_secret_arn          = module.secrets.secret_arns["truckit-api"]
  ml_pricing_secret_arn       = module.secrets.secret_arns["ml-pricing"]
  llm_secret_arn              = module.secrets.secret_arns["llm-api-key"]
  db_secret_arn               = module.data.db_secret_arn
  redis_secret_arn            = module.data.redis_secret_arn

  app_url_param         = module.secrets.app_url_parameter_name
  image_tag_param       = module.secrets.image_tag_parameter_name
  oauth_redirects_param = module.secrets.oauth_redirect_uris_parameter_name
  feature_flags_param   = module.secrets.feature_flags_parameter_name

  tags = {
    Name = var.name
  }

  depends_on = [module.edge, module.data, module.secrets]
}
