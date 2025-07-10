package logging

import (
	"log"
	"os"
	"time"

	"prac/pkg/encryption"
	"prac/pkg/uploader"
)

var RemoteLogger *log.Logger
var logFile *os.File

func Init() {
	err := os.MkdirAll("logs", 0755)
	if err != nil {
		log.Fatalf("No se pudo crear la carpeta de logs: %v", err)
	}

	logFile, err = os.OpenFile("logs/remote.log", os.O_CREATE|os.O_WRONLY|os.O_APPEND, 0666)
	if err != nil {
		log.Fatalf("No se pudo abrir el archivo de log remoto: %v", err)
	}

	RemoteLogger = log.New(logFile, "[remote] ", log.Ldate|log.Ltime|log.Lshortfile)
}

// ⚠️ Llama a esta función para cifrar, subir y borrar el log local
func EncryptAndUploadLog(password string, salt []byte, uploadURL string) error {
	logFile.Close() // Asegura que se cierre y se guarden todos los datos

	inputPath := "logs/remote.log"
	timestamp := time.Now().Format("20060102_150405")
	encPath := "logs/remote_" + timestamp + ".enc"

	key := encryption.DeriveKeyFromPassword(password, salt)

	err := encryption.EncryptFileAES(inputPath, encPath, key)
	if err != nil {
		return err
	}

	// Envíalo a backup-server, con nombre 'log-fecha.log.enc'
	destName := "log_" + timestamp + ".log.enc"
	err = uploader.UploadFileWithName(encPath, uploadURL, destName)
	if err != nil {
		return err
	}
	os.Remove(encPath)

	// Vaciar (truncate) el archivo de log original
	f, err := os.OpenFile(inputPath, os.O_TRUNC|os.O_WRONLY, 0666)
	if err != nil {
		return err
	}
	f.Close()

	// Reabrir el archivo para seguir usándolo como log
	logFile, err = os.OpenFile("logs/remote.log", os.O_CREATE|os.O_WRONLY|os.O_APPEND, 0666)
	if err != nil {
		return err
	}
	RemoteLogger.SetOutput(logFile)

	return nil
}

func StartLogBackupScheduler(password string, salt []byte, uploadURL string, interval time.Duration) {
	go func() {
		for {
			err := EncryptAndUploadLog(password, salt, uploadURL)
			if err != nil {
				log.Printf("Error al subir log cifrado: %v", err)
			}
			time.Sleep(interval)
		}
	}()
}
