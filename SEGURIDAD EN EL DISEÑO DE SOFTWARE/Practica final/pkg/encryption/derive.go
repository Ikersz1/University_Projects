package encryption

import (
	"golang.org/x/crypto/argon2"
)

// DeriveKeyFromPassword genera una clave AES (32 bytes) desde contraseña + salt
func DeriveKeyFromPassword(password string, salt []byte) []byte {
	return argon2.IDKey([]byte(password), salt, 3, 64*1024, 4, 32)
}
