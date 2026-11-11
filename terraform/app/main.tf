# Deploys the app onto an existing cluster from the same k8s/ manifests the
# pipeline used with kubectl, so the YAML stays the single source of truth.
# This is a separate root from ../ because kubernetes_manifest needs a
# reachable API server at plan time.
locals {
  k8s_dir   = "${path.module}/../../k8s"
  namespace = yamldecode(file("${local.k8s_dir}/namespace.yaml"))
  secret    = yamldecode(file("${local.k8s_dir}/mysql-secret.yaml"))

  manifests = {
    mysql_deployment = "mysql-deployment.yaml"
    mysql_service    = "mysql-service.yaml"
    app_deployment   = "app-deployment.yaml"
    app_service      = "app-service.yaml"
  }
}

resource "kubernetes_namespace_v1" "this" {
  metadata {
    name = local.namespace.metadata.name
  }
}

# Typed resources instead of kubernetes_manifest: the API server rewrites a
# Secret's stringData into data, which kubernetes_manifest reports as drift.
resource "kubernetes_secret_v1" "mysql_credentials" {
  metadata {
    name      = local.secret.metadata.name
    namespace = kubernetes_namespace_v1.this.metadata[0].name
  }
  type = local.secret.type
  data = local.secret.stringData
}

# Same as `kubectl create configmap mysql-init --from-file=init.sql=db/init.sql`.
resource "kubernetes_config_map_v1" "mysql_init" {
  metadata {
    name      = "mysql-init"
    namespace = kubernetes_namespace_v1.this.metadata[0].name
  }
  data = {
    "init.sql" = file("${path.module}/../../db/init.sql")
  }
}

# Rollout is not awaited here; the pipeline's "Wait for rollout" step does
# that and prints pod logs on failure.
resource "kubernetes_manifest" "this" {
  for_each = local.manifests

  manifest = yamldecode(replace(file("${local.k8s_dir}/${each.value}"), "IMAGE_PLACEHOLDER", var.app_image))

  depends_on = [
    kubernetes_secret_v1.mysql_credentials,
    kubernetes_config_map_v1.mysql_init,
  ]
}
