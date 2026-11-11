variable "cluster_name" {
  description = "Name of the kind cluster. Matches the cluster_name used by the validate-k8s-deploy job in .github/workflows/pipeline.yml."
  type        = string
  default     = "cicd-demo"
}

variable "node_image" {
  description = "kindest/node image for every node. v1.30.0 is the default of kind v0.23.0, which helm/kind-action v1.10.0 installs in the pipeline."
  type        = string
  default     = "kindest/node:v1.30.0"
}
