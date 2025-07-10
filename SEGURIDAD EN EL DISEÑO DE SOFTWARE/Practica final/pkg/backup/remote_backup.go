package backup

import (
	"fmt"
	"io"
	"log"
	"os"
	"path/filepath"
	"time"

	"prac/pkg/encryption"
	"prac/pkg/logging"
	"prac/pkg/uploader"
)

func StartBackupScheduler(sourceDir, backupDir string, interval time.Duration, password string, salt []byte, uploadURL string) {
	go func() {
		for {
			err := performBackup(sourceDir, backupDir, password, salt, uploadURL)
			if err != nil {
				logging.RemoteLogger.Printf("Error al hacer backup: %v", err)
			} else {
				logging.RemoteLogger.Println("Backup completado correctamente.")
			}
			time.Sleep(interval)
		}
	}()
}

func performBackup(sourceDir, backupDir string, password string, salt []byte, uploadURL string) error {
	timestamp := time.Now().Format("20060102_150405")
	destDir := filepath.Join(backupDir, timestamp)
	err := os.MkdirAll(destDir, 0755)
	if err != nil {
		return err
	}
	return filepath.Walk(sourceDir, func(path string, info os.FileInfo, err error) error {
		if err != nil {
			return err
		}
		if info.IsDir() {
			return nil
		}
		relPath, err := filepath.Rel(sourceDir, path)
		if err != nil {
			return err
		}
		destPath := filepath.Join(destDir, relPath)

		err = copyFile(path, destPath)
		if err != nil {
			return err
		}

		// Ahora procesamos el archivo en el destino (cifrar, subir, borrar)
		return ProcesarArchivoBackup(destPath, password, salt, uploadURL)
	})
}

func copyFile(src, dst string) error {
	from, err := os.Open(src)
	if err != nil {
		return err
	}
	defer from.Close()
	to, err := os.Create(dst)
	if err != nil {
		return err
	}
	defer to.Close()
	_, err = io.Copy(to, from)
	return err
}

func ProcesarArchivoBackup(path string, password string, salt []byte, uploadURL string) error {
	key := encryption.DeriveKeyFromPassword(password, salt)
	encryptedPath := path + ".enc"

	err := encryption.EncryptFileAES(path, encryptedPath, key)
	if err != nil {
		return err
	}

	timestamp := time.Now().Format("20060102_150405")
	baseName := filepath.Base(encryptedPath)
	filename := fmt.Sprintf("%s_%s", timestamp, baseName)
	err = uploader.UploadFileWithName(encryptedPath, uploadURL, filename)
	if err != nil {
		return err
	}

	// Borramos archivos originales y cifrados tras subirlos
	err = os.Remove(path)
	if err != nil {
		log.Printf("Error borrando archivo original %s: %v", path, err)
	}

	err = os.Remove(encryptedPath)
	if err != nil {
		log.Printf("Error borrando archivo cifrado %s: %v", encryptedPath, err)
	}

	return nil
}
