# Local kind (Kubernetes-in-Docker) cluster with one control plane node and
# var.worker_count workers, replacing `kind create cluster --name cicd-demo`.
# Runs entirely in Docker on this machine: no cloud account, no cost.
resource "kind_cluster" "this" {
  name           = var.cluster_name
  node_image     = var.node_image
  wait_for_ready = true

  # Written next to this config and gitignored (*.kubeconfig), so applying
  # does not touch ~/.kube/config.
  kubeconfig_path = "${abspath(path.module)}/${var.cluster_name}.kubeconfig"

  kind_config {
    kind        = "Cluster"
    api_version = "kind.x-k8s.io/v1alpha4"

    node {
      role = "control-plane"
    }

    dynamic "node" {
      for_each = range(var.worker_count)
      content {
        role = "worker"
      }
    }
  }
}
