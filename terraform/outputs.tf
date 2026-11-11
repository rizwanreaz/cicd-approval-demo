output "cluster_endpoint" {
  description = "Kubernetes API server endpoint of the kind cluster."
  value       = kind_cluster.this.endpoint
}

output "kubeconfig_path" {
  description = "Path of the generated kubeconfig. Use with: export KUBECONFIG=$(terraform output -raw kubeconfig_path)"
  value       = kind_cluster.this.kubeconfig_path
}
