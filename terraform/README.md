# Terraform: local kind cluster and app deploy

This folder provisions the kind cluster that the pipeline uses to validate the Kubernetes deploy, and then deploys the app onto it. Everything runs in Docker on your own machine or on the GitHub runner, so there are no cloud resources and no costs.

## What it provisions

There are two Terraform configurations, applied one after the other.

The configuration in this folder creates one kind cluster with one control plane node and one worker node, using the `tehcyx/kind` provider. Terraform waits until the control plane is ready before it finishes. The kubeconfig is written to `terraform/<cluster name>.kubeconfig`, so your normal `~/.kube/config` is left alone.

The configuration in `app/` deploys the manifests from `../k8s` onto that cluster with the `hashicorp/kubernetes` provider. It reads the YAML files directly, so `k8s/` stays the single source of truth. It also creates the `mysql-init` ConfigMap from `db/init.sql` and swaps `IMAGE_PLACEHOLDER` for the image you pass in. It lives in its own folder because the Kubernetes provider needs a running cluster before it can plan.

The node image is pinned by digest to `kindest/node:v1.37.0`. The provider has kind v0.33.0 built in, and kind only guarantees node images that were built for its own release, so change `node_image` only to another image listed in the kind v0.33.0 release notes.

## Environments

Each pipeline stage has its own variables file in `environments/` and its own Terraform workspace, so their state never mixes.

| Workspace | Variables file | Cluster name |
| --- | --- | --- |
| development | `environments/development.tfvars` | `cicd-demo-dev` |
| testing | `environments/testing.tfvars` | `cicd-demo-test` |
| production | `environments/production.tfvars` | `cicd-demo` |

The production cluster name matches the one the pipeline loads images into. You can also change `worker_count` per environment.

## Requirements

You need Docker running, Terraform 1.5 or newer, `kind` and `kubectl`. On macOS:

```sh
brew tap hashicorp/tap
brew install hashicorp/tap/terraform kind kubectl
```

## Usage

Create the cluster from inside the `terraform` folder. This example uses the development environment.

```sh
terraform init
terraform workspace select -or-create=true development
terraform plan  -var-file=environments/development.tfvars
terraform apply -var-file=environments/development.tfvars
export KUBECONFIG="$(terraform output -raw kubeconfig_path)"
kubectl get nodes
```

Build the app image, load it into the cluster and deploy it from the `app` folder.

```sh
docker build -t cicd-approval-demo:local ..
kind load docker-image cicd-approval-demo:local mysql:8.0 --name cicd-demo-dev
cd app
terraform init
terraform apply -var "kubeconfig_path=$KUBECONFIG" -var "app_image=cicd-approval-demo:local"
kubectl -n production port-forward svc/app 8080:80
```

Then open http://localhost:8080/login.php. To clean up, delete the cluster from the `terraform` folder. That also removes everything that was deployed onto it.

```sh
terraform destroy -var-file=environments/development.tfvars
```

If you also want to clear the state in `app/`, run `terraform destroy` there first with the same two variables.

## How it fits into the pipeline

The `validate-k8s-deploy` job in `.github/workflows/pipeline.yml` runs on pushes to `main`. It applies this folder with the production workspace and variables file, loads the freshly built image with `kind load`, applies `app/` with that image, waits for the rollouts, smoke tests the login page through the Service and finally runs `terraform destroy`.

The separate `.github/workflows/terraform.yml` workflow runs on pull requests that change this folder. It checks formatting and runs `terraform init` and `terraform validate` for both configurations, but never runs `plan` or `apply`.

State is local. That is fine here because every cluster is short lived: a fresh runner in CI, or your own machine locally.
