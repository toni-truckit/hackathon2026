variable "name" {
  description = "Name prefix, for example truckit-mcp-sandbox."
  type        = string
}

variable "vpc_cidr" {
  description = "VPC CIDR."
  type        = string
  default     = "10.0.0.0/16"
}
