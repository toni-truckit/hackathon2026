variable "name" {
  description = "Name prefix, for example truckit-mcp-sandbox."
  type        = string
}

variable "env" {
  description = "Environment name, for example sandbox."
  type        = string
}

variable "private_subnet_ids" {
  description = "Private subnets for the DB subnet groups. Two AZs required."
  type        = list(string)
}

variable "rds_sg_id" {
  description = "Security group for MySQL."
  type        = string
}

variable "redis_sg_id" {
  description = "Security group for Redis."
  type        = string
}

variable "db_instance_class" {
  description = "RDS instance class."
  type        = string
}

variable "db_backup_retention_days" {
  description = "Automated backup retention."
  type        = number
}

variable "redis_node_type" {
  description = "ElastiCache node type."
  type        = string
}

variable "redis_engine_version" {
  description = "Redis engine version."
  type        = string
}
