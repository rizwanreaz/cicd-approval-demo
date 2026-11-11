# Terraform: local kind cluster

This folder provisions the same kind cluster that the pipeline uses to validate the Kubernetes manifests, but declares it with Terraform instead of creating it by hand. Everything runs in Docker on your own machine, so there are no cloud resources and no costs.

## What it provisions

Terraform creates one kind cluster named `cicd-demo` with one control plane node and one worker node. The node image defaults to `kindest/node:v1.30.0`, which is the Kubernetes version the pipeline gets from `helm/kind-action@v1.10.0`. Terraform waits until the control plane is ready before it finishes.

The kubeconfig for the cluster is written to `terraform/cicd-demo.kubeconfig`, so your normal `~/.kube/config` is left alone. That file is ignored by git, as are the state files and the `.terraform` folder.

You can change the cluster name or node image in `variables.tf` or on the command line with `-var`.

## Requirements

You need Docker running and Terraform 1.5 or newer. On macOS you can install Terraform with Homebrew:

```sh
brew tap hashicorp/tap
brew install hashicorp/tap/terraform
```

## Usage

Run these commands from inside the `terraform` folder.

```sh
terraform init      # downloads the tehcyx/kind provider
terraform plan      # shows what will be created
terraform apply     # creates the cluster
terraform destroy   # deletes the cluster again
```

After an apply you can point kubectl at the new cluster and deploy the app the same way the pipeline does:

```sh
export KUBECONFIG="$(terraform output -raw kubeconfig_path)"
kubectl get nodes
kubectl apply -f ../k8s/namespace.yaml
```

The `kind create cluster --name cicd-demo` command in the main README creates a cluster with the same name, so delete that one first if you already have it running.

## How it fits into the pipeline

Today the `validate-k8s-deploy` job in `.github/workflows/pipeline.yml` creates its throwaway cluster with `helm/kind-action`. This folder describes that same cluster as code, so it can be reviewed, versioned and recreated the same way every time.

A separate workflow, `.github/workflows/terraform.yml`, runs on pull requests that change this folder. It only checks formatting, runs `terraform init` and runs `terraform validate`. It never runs `apply`, and the existing pipeline is not changed. Switching the validation job over to `terraform apply` is a later step.
