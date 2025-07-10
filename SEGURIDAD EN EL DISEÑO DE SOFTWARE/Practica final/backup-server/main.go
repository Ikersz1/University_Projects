package main

import (
	"fmt"
	"io"
	"net/http"
	"os"
	"path/filepath"
	"time"
)

func main() {
	http.HandleFunc("/upload", handleUpload)

	fmt.Println("Servidor de respaldo escuchando en :8081...")
	http.ListenAndServe(":8081", nil)
}

func handleUpload(w http.ResponseWriter, r *http.Request) {
	r.ParseMultipartForm(10 << 20) // 10MB máx

	file, handler, err := r.FormFile("file")
	if err != nil {
		http.Error(w, "Error al leer archivo", http.StatusBadRequest)
		return
	}
	defer file.Close()

	// Crear carpeta con fecha
	folder := time.Now().Format("20060102")
	dstDir := filepath.Join("storage", "files", folder)
	os.MkdirAll(dstDir, 0755)

	dstPath := filepath.Join(dstDir, handler.Filename)

	dst, err := os.Create(dstPath)
	if err != nil {
		http.Error(w, "Error al guardar archivo", http.StatusInternalServerError)
		return
	}
	defer dst.Close()

	_, err = io.Copy(dst, file)
	if err != nil {
		http.Error(w, "Error al copiar archivo", http.StatusInternalServerError)
		return
	}

	fmt.Fprintf(w, "Archivo %s recibido correctamente\n", handler.Filename)
}
