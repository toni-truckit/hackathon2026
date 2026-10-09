variable "region" {
  type    = string
  default = "ap-southeast-2"
}

variable "env" {
  type    = string
  default = "sandbox"
}

variable "owner" {
  type    = string
  default = "hackathon"
}

variable "name" {
  description = "Resource name prefix."
  type        = string
  default     = "truckit-mcp-sandbox"
}

variable "hostname" {
  description = "Public MCP hostname."
  type        = string
}

variable "hosted_zone_name" {
  description = "Route 53 public zone name, with trailing dot optional."
  type        = string
}

variable "vpc_cidr" {
  type    = string
  default = "10.42.0.0/16"
}

variable "instance_type" {
  type    = string
  default = "t3.small"
}

variable "db_instance_class" {
  type    = string
  default = "db.t4g.micro"
}

variable "redis_node_type" {
  type    = string
  default = "cache.t4g.micro"
}

variable "redis_engine_version" {
  type    = string
  default = "7.1"
}

variable "waf_mcp_rate_limit" {
  type    = number
  default = 500
}

variable "waf_oauth_rate_limit" {
  type    = number
  default = 200
}

variable "waf_catchall_rate_limit" {
  type    = number
  default = 2000
}
