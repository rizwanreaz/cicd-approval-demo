variable "kubeconfig_path" {
  description = "Kubeconfig of the target cluster, normally the kubeconfig_path output of the cluster config in the parent folder."
  type        = string
}

variable "app_image" {
  description = "App image substituted for IMAGE_PLACEHOLDER in k8s/app-deployment.yaml, e.g. ghcr.io/<owner>/<repo>:<sha> or cicd-approval-demo:local."
  type        = string
}
