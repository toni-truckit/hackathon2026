variable "region" {
  description = "Region for the state bucket."
  type        = string
  default     = "ap-southeast-2"
}

variable "owner" {
  description = "Owner tag."
  type        = string
  default     = "hackathon"
}

variable "state_reader_arns" {
  description = "IAM principals allowed to read state objects. Include the role ARN and the assumed-role ARN (arn:aws:sts::ACCOUNT:assumed-role/ROLE/*). Empty skips the deny, so the first apply cannot lock you out."
  type        = list(string)
  default     = []
}
