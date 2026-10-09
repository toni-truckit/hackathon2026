output "mcp_url" {
  value = "https://${var.hostname}"
}

output "alb_dns_name" {
  value = module.edge.alb_dns_name
}

output "ecr_repository_url" {
  value = module.compute.ecr_repository_url
}

output "asg_name" {
  value = module.compute.asg_name
}

output "rds_endpoint" {
  value = module.data.rds_endpoint
}

output "redis_endpoint" {
  value = module.data.redis_primary_endpoint
}

output "photos_bucket_name" {
  value = module.storage.photos_bucket_name
}
