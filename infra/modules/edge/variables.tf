variable "name" {
  description = "Name prefix, for example truckit-mcp-sandbox."
  type        = string
}

variable "hostname" {
  description = "Public hostname, without scheme."
  type        = string
}

variable "hosted_zone_name" {
  description = "Existing public Route 53 zone name."
  type        = string
}

variable "vpc_id" {
  description = "VPC for the target group."
  type        = string
}

variable "public_subnet_ids" {
  description = "Public subnets for the ALB."
  type        = list(string)
}

variable "alb_sg_id" {
  description = "ALB security group."
  type        = string
}

variable "logs_bucket_name" {
  description = "Bucket that receives ALB access logs."
  type        = string
}

variable "alb_logs_prefix" {
  description = "Prefix inside the access-logs bucket."
  type        = string
}

variable "waf_mcp_rate_limit" {
  description = "Max /mcp requests per 5 minutes for one Authorization header."
  type        = number
}

variable "waf_oauth_rate_limit" {
  description = "Max /oauth/token and /oauth/register requests per 5 minutes per IP."
  type        = number
}

variable "waf_catchall_rate_limit" {
  description = "Max requests per 5 minutes per IP, flood guard only."
  type        = number
}
