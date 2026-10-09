output "secret_arns" {
  value = { for key, secret in aws_secretsmanager_secret.app : key => secret.arn }
}

output "app_url_parameter_name" {
  value = aws_ssm_parameter.app_url.name
}

output "image_tag_parameter_name" {
  value = aws_ssm_parameter.image_tag.name
}

output "oauth_redirect_uris_parameter_name" {
  value = aws_ssm_parameter.oauth_redirect_uris.name
}

output "feature_flags_parameter_name" {
  value = aws_ssm_parameter.feature_flags.name
}
