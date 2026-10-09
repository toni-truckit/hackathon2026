terraform {
  backend "s3" {
    bucket         = "truckit-mcp-tfstate-349139559071"
    key            = "sandbox/terraform.tfstate"
    region         = "ap-southeast-2"
    dynamodb_table = "truckit-mcp-tfstate-lock"
    encrypt        = true
  }
}
