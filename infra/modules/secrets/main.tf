locals {
  # Placeholders only. Real values are set with put-secret-value and ignored here
  # so they stay out of later plans.
  placeholders = {
    app-key              = "CHANGE_ME"
    passport-private-key = "CHANGE_ME"
    passport-public-key  = "CHANGE_ME"
    truckit-api = jsonencode({
      base_url      = "CHANGE_ME"
      client_id     = "CHANGE_ME"
      client_secret = "CHANGE_ME"
    })
    ml-pricing = jsonencode({
      url = "CHANGE_ME"
      key = "CHANGE_ME"
    })
    llm-api-key = "CHANGE_ME"
  }
}

resource "aws_secretsmanager_secret" "app" {
  for_each = local.placeholders

  name                    = "/truckit-mcp/${var.env}/${each.key}"
  description             = "Truckit MCP ${each.key}. Replace the placeholder outside Terraform."
  recovery_window_in_days = 0
}

resource "aws_secretsmanager_secret_version" "app" {
  for_each = local.placeholders

  secret_id     = aws_secretsmanager_secret.app[each.key].id
  secret_string = each.value

  lifecycle {
    ignore_changes = [secret_string]
  }
}

resource "aws_ssm_parameter" "app_url" {
  name  = "/truckit-mcp/${var.env}/app_url"
  type  = "String"
  value = "https://${var.hostname}"
}

resource "aws_ssm_parameter" "image_tag" {
  name  = "/truckit-mcp/${var.env}/image_tag"
  type  = "String"
  value = "placeholder"

  # Deploys update this parameter. Apply must not reset it.
  lifecycle {
    ignore_changes = [value]
  }
}

resource "aws_ssm_parameter" "oauth_redirect_uris" {
  name  = "/truckit-mcp/${var.env}/oauth_redirect_uris"
  type  = "String"
  value = "https://claude.ai/api/mcp/auth_callback"
}

resource "aws_ssm_parameter" "feature_flags" {
  name  = "/truckit-mcp/${var.env}/feature_flags"
  type  = "String"
  value = "{}"
}
