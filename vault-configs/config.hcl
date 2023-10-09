listener "tcp" {
  address     = "0.0.0.0:8200"
  tls_cert_file = "./tls/cert.pem"
  tls_key_file  = "./tls/key.pem"
}

storage "file" {
  path = "./vault-data"
}

disable_mlock = true