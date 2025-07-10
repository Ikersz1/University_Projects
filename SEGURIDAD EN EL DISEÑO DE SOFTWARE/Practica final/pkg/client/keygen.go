package client

import (
	"crypto/ecdsa"
	"crypto/elliptic"
	"crypto/rand"
	"crypto/sha256"
	"encoding/base64"
	"encoding/json"
	"errors"
	"fmt"
	"io/ioutil"

	"math/big"

	"golang.org/x/crypto/argon2"
)

type KeyPair struct {
	PrivateKey string // en base64 (cifrada con clave derivada)
	PublicKey  string // en base64
}

func generateKeyPair() (*ecdsa.PrivateKey, error) {
	return ecdsa.GenerateKey(elliptic.P256(), rand.Reader)
}

func exportPublicKey(pub *ecdsa.PublicKey) string {
	xBytes := pub.X.Bytes()
	yBytes := pub.Y.Bytes()
	data := append(xBytes, yBytes...)
	return base64.StdEncoding.EncodeToString(data)
}

func exportPrivateKey(priv *ecdsa.PrivateKey, password string) (string, error) {
	salt := make([]byte, 16)
	_, err := rand.Read(salt)
	if err != nil {
		return "", err
	}

	key := argon2.IDKey([]byte(password), salt, 3, 64*1024, 4, 32)

	privBytes := priv.D.Bytes()
	hash := sha256.New()
	hash.Write(key)
	hashKey := hash.Sum(nil)

	enc := xorEncrypt(privBytes, hashKey)
	bundle := append(salt, enc...)
	return base64.StdEncoding.EncodeToString(bundle), nil
}

func xorEncrypt(data, key []byte) []byte {
	out := make([]byte, len(data))
	for i := range data {
		out[i] = data[i] ^ key[i%len(key)]
	}
	return out
}

func saveKeyPairToDisk(filePath string, pair KeyPair) error {
	data, err := json.MarshalIndent(pair, "", "  ")
	if err != nil {
		return err
	}
	return ioutil.WriteFile(filePath, data, 0600)
}

func LoadKeyPairFromDisk(filePath string) (*KeyPair, error) {
	data, err := ioutil.ReadFile(filePath)
	if err != nil {
		return nil, err
	}
	var kp KeyPair
	err = json.Unmarshal(data, &kp)
	if err != nil {
		return nil, err
	}
	return &kp, nil
}

func SetupAndSaveKeyPair(username, password string) error {
	priv, err := generateKeyPair()
	if err != nil {
		return err
	}

	pubEncoded := exportPublicKey(&priv.PublicKey)
	privEncoded, err := exportPrivateKey(priv, password)
	if err != nil {
		return err
	}

	pair := KeyPair{
		PrivateKey: privEncoded,
		PublicKey:  pubEncoded,
	}

	filePath := fmt.Sprintf("%s_keypair.json", username)
	return saveKeyPairToDisk(filePath, pair)
}

func LoadPrivateKeyFromDisk(username, password string) (*ecdsa.PrivateKey, error) {
	filePath := fmt.Sprintf("%s_keypair.json", username)
	kp, err := LoadKeyPairFromDisk(filePath)
	if err != nil {
		return nil, err
	}

	decoded, err := base64.StdEncoding.DecodeString(kp.PrivateKey)
	if err != nil || len(decoded) < 16 {
		return nil, errors.New("clave privada malformada")
	}

	salt := decoded[:16]
	ciphertext := decoded[16:]

	key := argon2.IDKey([]byte(password), salt, 3, 64*1024, 4, 32)
	hash := sha256.New()
	hash.Write(key)
	hashKey := hash.Sum(nil)

	plain := xorEncrypt(ciphertext, hashKey)
	priv := new(ecdsa.PrivateKey)
	priv.PublicKey.Curve = elliptic.P256()
	priv.D = new(big.Int).SetBytes(plain)
	priv.PublicKey.X, priv.PublicKey.Y = priv.PublicKey.Curve.ScalarBaseMult(plain)

	return priv, nil
}

func DeriveSharedKey(priv *ecdsa.PrivateKey, recipientPubB64 string) ([]byte, error) {
	decoded, err := base64.StdEncoding.DecodeString(recipientPubB64)
	if err != nil {
		return nil, err
	}
	if len(decoded)%2 != 0 {
		return nil, errors.New("clave pública inválida")
	}

	x := new(big.Int).SetBytes(decoded[:len(decoded)/2])
	y := new(big.Int).SetBytes(decoded[len(decoded)/2:])

	xShared, _ := priv.PublicKey.Curve.ScalarMult(x, y, priv.D.Bytes())
	shared := sha256.Sum256(xShared.Bytes())
	return shared[:], nil
}
