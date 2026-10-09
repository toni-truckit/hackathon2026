output "photos_bucket_name" {
  value = aws_s3_bucket.photos.id
}

output "photos_bucket_arn" {
  value = aws_s3_bucket.photos.arn
}

output "logs_bucket_name" {
  value = aws_s3_bucket.logs.id

  depends_on = [aws_s3_bucket_policy.logs]
}

output "alb_logs_prefix" {
  value = local.alb_logs_prefix
}
