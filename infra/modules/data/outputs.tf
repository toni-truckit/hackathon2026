output "rds_identifier" {
  value = aws_db_instance.this.identifier
}

output "rds_endpoint" {
  value = aws_db_instance.this.address
}

output "db_secret_arn" {
  value = aws_secretsmanager_secret.db.arn
}

output "redis_primary_endpoint" {
  value = aws_elasticache_replication_group.this.primary_endpoint_address
}

output "redis_replication_group_id" {
  value = aws_elasticache_replication_group.this.id
}

output "redis_cache_cluster_id" {
  value = one(aws_elasticache_replication_group.this.member_clusters)
}

output "redis_secret_arn" {
  value = aws_secretsmanager_secret.redis.arn
}
