variable "name" {
  description = "Name prefix, for example truckit-mcp-sandbox."
  type        = string
}

variable "env" {
  description = "Environment name."
  type        = string
}

variable "asg_name" {
  description = "Auto Scaling group name. Also the SSM deploy target."
  type        = string
}

variable "app_subnet_ids" {
  description = "Private subnets for the ASG. Sandbox passes one."
  type        = list(string)
}

variable "app_sg_id" {
  description = "App security group."
  type        = string
}

variable "target_group_arn" {
  description = "ALB target group."
  type        = string
}

variable "instance_type" {
  description = "EC2 instance type."
  type        = string
}

variable "photos_bucket_arn" {
  description = "Item photo bucket ARN."
  type        = string
}

variable "secret_arns" {
  description = "Secrets the instance may read."
  type        = list(string)
}

variable "parameter_arns" {
  description = "SSM parameters the instance may read."
  type        = list(string)
}

variable "app_key_secret_arn" {
  type = string
}

variable "passport_private_secret_arn" {
  type = string
}

variable "passport_public_secret_arn" {
  type = string
}

variable "truckit_secret_arn" {
  type = string
}

variable "ml_pricing_secret_arn" {
  type = string
}

variable "llm_secret_arn" {
  type = string
}

variable "db_secret_arn" {
  type = string
}

variable "redis_secret_arn" {
  type = string
}

variable "app_url_param" {
  type = string
}

variable "image_tag_param" {
  type = string
}

variable "oauth_redirects_param" {
  type = string
}

variable "feature_flags_param" {
  type = string
}

variable "tags" {
  description = "Tags copied onto instances launched by the template."
  type        = map(string)
}

variable "log_retention_days" {
  description = "CloudWatch log retention for app and nginx groups."
  type        = number
  default     = 14
}
