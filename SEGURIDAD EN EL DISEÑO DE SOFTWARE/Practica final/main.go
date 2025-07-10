package main

import (
	"log"
	"os"
	"time"

	"prac/pkg/backup"
	"prac/pkg/client"
	"prac/pkg/logging"
	"prac/pkg/server"
	"prac/pkg/ui"

	"github.com/joho/godotenv"
)

func main() {
	err := godotenv.Load()
	if err != nil {
		log.Fatalf("Error cargando archivo .env: %v", err)
	}

	password := os.Getenv("BACKUP_PASSWORD")
	saltStr := os.Getenv("BACKUP_SALT")
	uploadURL := os.Getenv("BACKUP_SERVER")

	if len(saltStr) != 16 {
		log.Fatalf("BACKUP_SALT debe tener 16 bytes, tienes: %d", len(saltStr))
	}
	salt := []byte(saltStr)

	log := log.New(os.Stdout, "[main] ", log.LstdFlags)

	log.Println("Iniciando sistema de logging remoto...")
	logging.Init()
	logging.RemoteLogger.Println("Logging remoto inicializado desde main.go")

	log.Println("Iniciando sistema de backup remoto...")
	backup.StartBackupScheduler("data", "backup", 1*time.Minute, password, salt, uploadURL)
	log.Println("Iniciando sistema de logs cifrados periódicos...")
	logging.StartLogBackupScheduler(password, salt, uploadURL, 2*time.Minute)

	log.Println("Iniciando servidor...")
	go func() {
		if err := server.Run(); err != nil {
			log.Fatalf("Error del servidor: %v\n", err)
		}
	}()

	const totalSteps = 20
	for i := 1; i <= totalSteps; i++ {
		ui.PrintProgressBar(i, totalSteps, 30)
		time.Sleep(100 * time.Millisecond)
	}

	log.Println("Cifrando y subiendo log remoto...")
	err = logging.EncryptAndUploadLog(password, salt, uploadURL)
	if err != nil {
		log.Printf("Error al subir log cifrado: %v", err)
	} else {
		log.Println("Log remoto cifrado y subido correctamente.")
	}

	log.Println("Iniciando cliente...")
	client.Run()
}
