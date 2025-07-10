package store

import (
	"bytes"
	"crypto/aes"
	"crypto/cipher"
	"crypto/rand"
	"encoding/hex"
	"fmt"
	"io"
	"log"
	"os"
	"strings"

	"github.com/joho/godotenv"
	"go.etcd.io/bbolt"
)

var masterKey []byte

/*
	Implementación de la interfaz Store mediante BoltDB (versión bbolt)
*/

// BboltStore contiene la instancia de la base de datos bbolt.
type BboltStore struct {
	db *bbolt.DB
}

func init() {
	_ = godotenv.Load(".env") // carga .env antes de acceder a AES_KEY

	keyHex := strings.TrimSpace(os.Getenv("AES_KEY"))
	// fmt.Printf("DEBUG en init() - AES_KEY='%s' (longitud: %d)\n", keyHex, len(keyHex))

	if len(keyHex) != 64 {
		log.Fatalf("AES_KEY debe tener 64 caracteres hexadecimales (32 bytes)")
	}

	key, err := hex.DecodeString(keyHex)
	if err != nil {
		log.Fatalf("Error al decodificar AES_KEY: %v", err)
	}

	masterKey = key
}

// encrypt cifra plaintext con AES-GCM devolviendo nonce|ciphertext.
func encrypt(plaintext []byte) ([]byte, error) {
	block, err := aes.NewCipher(masterKey)
	if err != nil {
		return nil, fmt.Errorf("aes.NewCipher: %w", err)
	}
	gcm, err := cipher.NewGCM(block)
	if err != nil {
		return nil, fmt.Errorf("cipher.NewGCM: %w", err)
	}

	nonce := make([]byte, gcm.NonceSize())
	if _, err := io.ReadFull(rand.Reader, nonce); err != nil {
		return nil, fmt.Errorf("leer nonce: %w", err)
	}

	ciphertext := gcm.Seal(nil, nonce, plaintext, nil)
	return append(nonce, ciphertext...), nil
}

// decrypt separa nonce|ciphertext y devuelve plaintext.
func decrypt(data []byte) ([]byte, error) {
	block, err := aes.NewCipher(masterKey)
	if err != nil {
		return nil, fmt.Errorf("aes.NewCipher: %w", err)
	}
	gcm, err := cipher.NewGCM(block)
	if err != nil {
		return nil, fmt.Errorf("cipher.NewGCM: %w", err)
	}

	if len(data) < gcm.NonceSize() {
		return nil, fmt.Errorf("datos demasiado cortos")
	}
	nonce := data[:gcm.NonceSize()]
	ciphertext := data[gcm.NonceSize():]

	plaintext, err := gcm.Open(nil, nonce, ciphertext, nil)
	if err != nil {
		return nil, fmt.Errorf("gcm.Open: %w", err)
	}
	return plaintext, nil
}

// NewBboltStore abre la base de datos bbolt en la ruta especificada.
func NewBboltStore(path string) (*BboltStore, error) {
	db, err := bbolt.Open(path, 0600, nil)
	if err != nil {
		return nil, fmt.Errorf("error al abrir base de datos bbolt: %v", err)
	}
	return &BboltStore{db: db}, nil
}

// Put almacena o actualiza (key, value) dentro de un bucket = namespace.
// No se soportan sub-buckets.
func (s *BboltStore) Put(namespace string, key, value []byte) error {
	encrypted, err := encrypt(value)
	if err != nil {
		return fmt.Errorf("cifrando valor: %v", err)
	}

	return s.db.Update(func(tx *bbolt.Tx) error {
		b, err := tx.CreateBucketIfNotExists([]byte(namespace))
		if err != nil {
			return fmt.Errorf("error al crear/abrir bucket '%s': %v", namespace, err)
		}
		return b.Put(key, encrypted)
	})
}

// Get recupera el valor de (key) en el bucket = namespace.
func (s *BboltStore) Get(namespace string, key []byte) ([]byte, error) {
	var encrypted []byte
	err := s.db.View(func(tx *bbolt.Tx) error {
		b := tx.Bucket([]byte(namespace))
		if b == nil {
			return fmt.Errorf("bucket no encontrado: %s", namespace)
		}
		encrypted = b.Get(key)
		if encrypted == nil {
			return fmt.Errorf("clave no encontrada: %s", string(key))
		}
		return nil
	})
	if err != nil {
		return nil, err
	}

	plaintext, err := decrypt(encrypted)
	if err != nil {
		return nil, fmt.Errorf("descifrando valor: %v", err)
	}
	return plaintext, nil
}

// Delete elimina la clave 'key' del bucket = namespace.
func (s *BboltStore) Delete(namespace string, key []byte) error {
	return s.db.Update(func(tx *bbolt.Tx) error {
		b := tx.Bucket([]byte(namespace))
		if b == nil {
			return fmt.Errorf("bucket no encontrado: %s", namespace)
		}
		return b.Delete(key)
	})
}

// ListKeys devuelve todas las claves del bucket = namespace.
func (s *BboltStore) ListKeys(namespace string) ([][]byte, error) {
	var keys [][]byte
	err := s.db.View(func(tx *bbolt.Tx) error {
		b := tx.Bucket([]byte(namespace))
		if b == nil {
			return fmt.Errorf("bucket no encontrado: %s", namespace)
		}
		c := b.Cursor()
		for k, _ := c.First(); k != nil; k, _ = c.Next() {
			kCopy := make([]byte, len(k))
			copy(kCopy, k)
			keys = append(keys, kCopy)
		}
		return nil
	})
	return keys, err
}

// KeysByPrefix devuelve las claves que inicien con 'prefix' en el bucket = namespace.
func (s *BboltStore) KeysByPrefix(namespace string, prefix []byte) ([][]byte, error) {
	var matchedKeys [][]byte
	err := s.db.View(func(tx *bbolt.Tx) error {
		b := tx.Bucket([]byte(namespace))
		if b == nil {
			return fmt.Errorf("bucket no encontrado: %s", namespace)
		}
		c := b.Cursor()
		for k, _ := c.Seek(prefix); k != nil && bytes.HasPrefix(k, prefix); k, _ = c.Next() {
			kCopy := make([]byte, len(k))
			copy(kCopy, k)
			matchedKeys = append(matchedKeys, kCopy)
		}
		return nil
	})
	return matchedKeys, err
}

// Close cierra la base de datos bbolt.
func (s *BboltStore) Close() error {
	return s.db.Close()
}

// Dump imprime todo el contenido de la base de datos bbolt para propósitos de depuración.
func (s *BboltStore) Dump() error {
	err := s.db.View(func(tx *bbolt.Tx) error {
		return tx.ForEach(func(bucketName []byte, b *bbolt.Bucket) error {
			fmt.Printf("Bucket: %s\n", string(bucketName))
			return b.ForEach(func(k, v []byte) error {
				fmt.Printf("  Key: %s, Value: %s\n", string(k), string(v))
				return nil
			})
		})
	})
	if err != nil {
		return fmt.Errorf("error al hacer el volcado de depuración: %v", err)
	}
	return nil
}

func (s *BboltStore) DumpWithDecipher() error {
	return s.db.View(func(tx *bbolt.Tx) error {
		return tx.ForEach(func(bucketName []byte, b *bbolt.Bucket) error {
			fmt.Printf("Bucket: %s\n", string(bucketName))
			return b.ForEach(func(k, v []byte) error {
				plain, err := decrypt(v)
				if err != nil {
					plain = []byte("[ERROR DESCIFRANDO]")
				}
				fmt.Printf("  Key: %s, Value: %s\n", string(k), string(plain))
				return nil
			})
		})
	})
}
