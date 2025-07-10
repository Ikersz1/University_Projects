package client

import (
	"crypto/aes"
	"crypto/cipher"
	"crypto/rand"
	"encoding/base64"
	"errors"
	"io"
)

var currentKey []byte

func SetKey(k []byte) {
	currentKey = k
}

func ClearKey() { currentKey = nil }

// EncryptMessage cifra el mensaje con AES y devuelve base64
func EncryptMessage(text string) (string, error) {
	if currentKey == nil {
		return "", errors.New("clave no inicializada")
	}

	block, err := aes.NewCipher(currentKey)
	if err != nil {
		return "", err
	}

	gcm, err := cipher.NewGCM(block)
	if err != nil {
		return "", err
	}

	nonce := make([]byte, gcm.NonceSize())
	if _, err = io.ReadFull(rand.Reader, nonce); err != nil {
		return "", err
	}

	ct := gcm.Seal(nil, nonce, []byte(text), nil)
	return base64.StdEncoding.EncodeToString(append(nonce, ct...)), nil
}

// DecryptMessage descifra el mensaje cifrado en base64
func DecryptMessage(encrypted string) (string, error) {
	if currentKey == nil {
		return "", errors.New("clave no inicializada")
	}

	data, err := base64.StdEncoding.DecodeString(encrypted)
	if err != nil {
		return "", err
	}

	block, err := aes.NewCipher(currentKey)
	if err != nil {
		return "", err
	}

	gcm, err := cipher.NewGCM(block)
	if err != nil {
		return "", err
	}

	if len(data) < gcm.NonceSize() {
		return "", errors.New("datos cortos")
	}

	nonce, ct := data[:gcm.NonceSize()], data[gcm.NonceSize():]
	pt, err := gcm.Open(nil, nonce, ct, nil)
	if err != nil {
		return "", err
	}

	return string(pt), nil
}
