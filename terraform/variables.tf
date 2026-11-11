variable "cluster_name" {
  description = "Name of the kind cluster. The production value matches the cluster name the validate-k8s-deploy job in .github/workflows/pipeline.yml loads images into."
  type        = string
  default     = "cicd-demo"
}

variable "node_image" {
  description = "kindest/node image for every node. Must be one built for the kind version embedded in the tehcyx/kind provider (kind v0.33.0 for provider 0.13.0), so it is pinned by digest as kind recommends."
  type        = string
  default     = "kindest/node:v1.37.0@sha256:a1ed56cfb0e7b93589bdf97c8cd566405a265939e3620fc4f5de89adff580ae5"
}

variable "worker_count" {
  description = "Number of worker nodes next to the single control plane node."
  type        = number
  default     = 1

  validation {
    condition     = var.worker_count >= 0
    error_message = "worker_count cannot be negative."
  }
}
