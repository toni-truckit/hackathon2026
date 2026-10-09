variable "name" {
  description = "Name prefix, for example truckit-mcp-sandbox."
  type        = string
}

variable "photos_expiration_days" {
  description = "Days before item photos expire."
  type        = number
  default     = 30
}
