output "namespace" {
  description = "Namespace the app was deployed into."
  value       = kubernetes_namespace_v1.this.metadata[0].name
}
