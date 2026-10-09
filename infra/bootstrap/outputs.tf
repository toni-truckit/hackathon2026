output "state_bucket_name" {
  description = "Put this in infra/envs/sandbox/backend.tf."
  value       = aws_s3_bucket.state.id
}

output "state_bucket_arn" {
  value = aws_s3_bucket.state.arn
}

output "region" {
  value = var.region
}

output "state_lock_table_name" {
  description = "Put this in infra/envs/sandbox/backend.tf."
  value       = aws_dynamodb_table.state_lock.name
}
