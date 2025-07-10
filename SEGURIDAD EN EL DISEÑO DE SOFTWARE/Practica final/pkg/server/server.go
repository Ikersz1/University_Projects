// El paquete server contiene el código del servidor.
// Interactúa con el cliente mediante una API JSON/HTTP
package server

import (
	"crypto/rand"
	"crypto/sha256"
	"crypto/subtle"
	"encoding/base64"
	"encoding/binary"
	"encoding/json"
	"fmt"
	"log"
	"net/http"
	"os"
	"regexp"
	"strings"
	"sync/atomic"
	"time"

	"golang.org/x/crypto/argon2"

	"prac/pkg/api"
	"prac/pkg/logging"
	"prac/pkg/store"
)

// Struct para las sessiones de los usuarios
type Session struct {
	Token     string    `json:"token"`
	ExpiresAt time.Time `json:"expires_at"`
}

// server encapsula el estado de nuestro servidor
type server struct {
	db           store.Store // base de datos
	log          *log.Logger // logger para mensajes de error e información
	tokenCounter int64       // contador para generar tokens
}

// Run inicia la base de datos y arranca el servidor HTTPS.
// (aqui incluimos lo de HTTPS y transporte seguro)
func Run() error {
	// Abrir la base de datos usando el motor bbolt.
	db, err := store.NewStore("bbolt", "data/server.db")
	if err != nil {
		return fmt.Errorf("error abriendo base de datos: %v", err)
	}

	// Crear el servidor con su logger.
	srv := &server{
		db:  db,
		log: log.New(os.Stdout, "[srv] ", log.LstdFlags),
	}

	// 🔐 Inicializar el usuario administrador si no existe
	if err := initAdminUser(srv); err != nil {
		return fmt.Errorf("error al inicializar el usuario admin: %v", err)
	}

	// Aseguramos que la base de datos se cierre al salir
	defer srv.db.Close()

	// Construir el mux y asociar /api al handler
	mux := http.NewServeMux()
	mux.Handle("/api", http.HandlerFunc(srv.apiHandler))

	// Iniciar servidor HTTPS
	fmt.Println("🚀 Servidor iniciado en https://localhost:8443")
	return http.ListenAndServeTLS(":8443", "certs/localhost.crt", "certs/localhost.key", mux)
}

// apiHandler descodifica la solicitud JSON, la despacha
// a la función correspondiente y devuelve la respuesta JSON.
func (s *server) apiHandler(w http.ResponseWriter, r *http.Request) {
	if r.Method != http.MethodPost {
		http.Error(w, "Método no permitido", http.StatusMethodNotAllowed)
		return
	}

	// Decodificamos la solicitud en una estructura api.Request
	var req api.Request
	if err := json.NewDecoder(r.Body).Decode(&req); err != nil {
		http.Error(w, "Error en el formato JSON", http.StatusBadRequest)
		return
	}

	// Despacho según la acción solicitada
	var res api.Response
	switch req.Action {
	case api.ActionRegister:
		logging.RemoteLogger.Printf("Registro de usuario: %s", req.Username)
		res = s.registerUser(req)

	case api.ActionLogin:
		logging.RemoteLogger.Printf("Inicio de sesión: %s", req.Username)
		res = s.loginUser(req)

	case api.ActionFetchData:
		logging.RemoteLogger.Printf("Petición de datos por: %s", req.Username)
		res = s.fetchData(req)

	case api.ActionUpdateData:
		logging.RemoteLogger.Printf("Actualización de datos por: %s", req.Username)
		res = s.updateData(req)

	case api.ActionLogout:
		logging.RemoteLogger.Printf("Logout del usuario: %s", req.Username)
		res = s.logoutUser(req)

	case api.ActionSendMessage:
		logging.RemoteLogger.Printf("Mensaje privado enviado por %s a %s", req.Username, req.Recipient)
		res = s.sendPrivateMessage(req)

	case api.ActionGetMessages:
		logging.RemoteLogger.Printf("Lectura de mensajes por: %s", req.Username)
		res = s.getMessages(req)

	case api.ActionCreateProposal:
		logging.RemoteLogger.Printf("Creación de propuesta por: %s - Propuesta: %s", req.Username, req.Extra)
		res = s.createProposal(req)

	case api.ActionGetProposals:
		logging.RemoteLogger.Printf("Consulta de propuestas por: %s", req.Username)
		res = s.getProposals(req)

	case api.ActionVote:
		logging.RemoteLogger.Printf("Votación por: %s", req.Username)
		res = s.voteProposal(req)

	case api.ActionGetVotes:
		logging.RemoteLogger.Printf("Consulta de votos por: %s", req.Username)
		res = s.getVotes(req)

	case api.ActionUploadPubKey:
		// En esta implementación, usamos esta acción para obtener la clave pública de un usuario.
		if req.Data == "" {
			res = api.Response{Success: false, Message: "Faltan datos"}
			break
		}
		pubkey, err := s.db.Get("pubkeys", []byte(req.Data))
		if err != nil {
			res = api.Response{Success: false, Message: "Error al obtener clave pública"}
		} else {
			res = api.Response{Success: true, Data: string(pubkey)}
		}

	case api.ActionCreateCategory:
		logging.RemoteLogger.Printf("Creación de categoría por: %s", req.Username)
		res = s.createCategory(req)

	case api.ActionAssignRole:
		logging.RemoteLogger.Printf("Asignación de rol por: %s -> %s = %s", req.Username, req.Recipient, req.Role)
		res = s.assignRole(req)

	case api.ActionListUsers:
		logging.RemoteLogger.Printf("Listado de usuarios solicitado por: %s", req.Username)
		res = s.listUsers(req)

	case api.ActionDeleteUserMessages:
		logging.RemoteLogger.Printf("Eliminación de mensajes solicitada por: %s sobre %s", req.Username, req.Data)
		res = s.deleteUserMessages(req)

	case api.ActionGetCategories:
		logging.RemoteLogger.Printf("Consulta de categorías por: %s", req.Username)
		res = s.getCategories(req)

	default:
		logging.RemoteLogger.Printf("Acción desconocida: %s - Usuario: %s", req.Action, req.Username)
		res = api.Response{Success: false, Message: "Acción desconocida"}
	}

	// Enviamos la respuesta en formato JSON
	w.Header().Set("Content-Type", "application/json")
	json.NewEncoder(w).Encode(res)
}

// generateToken genera un token seguro con crypto/rand
func (s *server) generateToken() (string, error) {
	b := make([]byte, 32)
	_, err := rand.Read(b)
	if err != nil {
		return "", err
	}
	return base64.RawURLEncoding.EncodeToString(b), nil
}

// Función auxiliar
// Convierte uint64 a []byte
func uint64ToBytes(u uint64) []byte {
	buf := make([]byte, 8)
	binary.BigEndian.PutUint64(buf, u)
	return buf
}

// Función auxiliar
// Convierte []byte a uint64 (devuelve 0 si hay error)
func bytesToUint64(b []byte) uint64 {
	if len(b) < 8 {
		return 0
	}
	return binary.BigEndian.Uint64(b)
}

// Función para comprobar si un nuevo username es válido usando una regex
// Solo acepta letras, números y guiones bajos y mínimo 5 carácteres
func isValidUsername(username string) bool {
	return regexp.MustCompile(`^[a-zA-Z0-9_]{5,20}$`).MatchString(username)
}

// Función para generar el salt aleatorio
func generateSalt() ([]byte, error) {
	salt := make([]byte, 16)
	_, err := rand.Read(salt)
	return salt, err
}

// Función para verificar password obteniendo el hash
// de la contraseña guardada en bbdd y hasheando
// la contraseña recibida con el mismo salt para verificar
func verifyPassword(password string, encodedHash string) bool {
	parts := strings.Split(encodedHash, "$")
	if len(parts) != 2 {
		return false
	}

	salt, err := base64.RawStdEncoding.DecodeString(parts[0])
	if err != nil {
		return false
	}

	hash, err := base64.RawStdEncoding.DecodeString(parts[1])
	if err != nil {
		return false
	}

	newHash := argon2.IDKey([]byte(password), salt, 3, 64*1024, 4, 32)

	// Evitar time attacks
	// si usasemos == comparamos byte por byte
	return subtle.ConstantTimeCompare(newHash, hash) == 1
}

func hashToken(token string) string {
	sum := sha256.Sum256([]byte(token))
	return base64.RawStdEncoding.EncodeToString(sum[:])
}

// Función para hashear la password del usuario
// Algoritmo: ARGON2
func hashPassword(password string) (string, error) {
	salt, err := generateSalt()
	if err != nil {
		return "", err
	}

	hash := argon2.IDKey([]byte(password), salt, 3, 64*1024, 4, 32)

	saltB64 := base64.RawStdEncoding.EncodeToString(salt)
	hashB64 := base64.RawStdEncoding.EncodeToString(hash)

	return fmt.Sprintf("%s$%s", saltB64, hashB64), nil
}

// registerUser registra un nuevo usuario, si no existe.
// Además, guarda la clave pública enviada por el cliente.
func (s *server) registerUser(req api.Request) api.Response {
	// Validación básica
	if req.Username == "" || req.Password == "" || req.Extra == "" {
		return api.Response{Success: false, Message: "Faltan credenciales o clave pública"}
	}

	// Verificamos si ya existe el usuario en 'auth'
	exists, err := s.userExists(req.Username)
	if err != nil {
		return api.Response{Success: false, Message: "Error al verificar usuario"}
	}
	if exists {
		return api.Response{Success: false, Message: "El usuario ya existe"}
	}

	// Validamos formato del username
	if !isValidUsername(req.Username) {
		return api.Response{
			Success: false,
			Message: "Nombre de usuario inválido (5-20 carácteres, solo letras, números y _)",
		}
	}

	// Hasheamos la contraseña y la almacenamos
	hashed, err := hashPassword(req.Password)
	if err != nil {
		return api.Response{Success: false, Message: "Error al hashear password"}
	}
	if err := s.db.Put("auth", []byte(req.Username), []byte(hashed)); err != nil {
		return api.Response{Success: false, Message: "Error al guardar credenciales"}
	}

	// Inicializamos espacio en 'userdata'
	if err := s.db.Put("userdata", []byte(req.Username), []byte("")); err != nil {
		return api.Response{Success: false, Message: "Error al inicializar datos de usuario"}
	}

	// Guardamos la clave pública en el bucket 'pubkeys'
	if err := s.db.Put("pubkeys", []byte(req.Username), []byte(req.Extra)); err != nil {
		return api.Response{Success: false, Message: "Error al guardar clave pública"}
	}

	// Asignamos rol por defecto 'normal'
	if err := s.db.Put("roles", []byte(req.Username), []byte("normal")); err != nil {
		return api.Response{Success: false, Message: "Error al asignar rol por defecto"}
	}

	return api.Response{Success: true, Message: "Usuario registrado correctamente"}
}

// loginUser valida credenciales en el namespace 'auth' y genera un token en 'sessions'.
func (s *server) loginUser(req api.Request) api.Response {
	if req.Username == "" || req.Password == "" {
		return api.Response{Success: false, Message: "Faltan credenciales"}
	}

	// Comprobamos los intentos de inicio de sesión
	login_attempts, _ := s.db.Get("login_attempts", []byte(req.Username))
	attempts := bytesToUint64(login_attempts)

	lastAttemptBytes, _ := s.db.Get("login_attempts_time", []byte(req.Username))
	lastAttemptTime := bytesToUint64(lastAttemptBytes)

	// En caso de más de 5 intentos fallidos, bloqueamos temporalmente la cuenta
	if attempts >= 5 {
		if time.Since(time.Unix(int64(lastAttemptTime), 0)) < 10*time.Minute {
			return api.Response{Success: false, Message: "Cuenta bloqueada por 10 minutos"}
		} else {
			// Reseteamos los contadores de intento de login
			s.db.Delete("login_attempts", []byte(req.Username))
			s.db.Delete("login_attempts_time", []byte(req.Username))
			attempts = 0
		}
	}

	// Recogemos la contraseña guardada en 'auth'
	// En caso de no encontrar el usuario, devolvemos credenciales inválidas
	storedPass, err := s.db.Get("auth", []byte(req.Username))
	if err != nil {
		return api.Response{Success: false, Message: "Credenciales inválidas"}
	}

	parts := strings.Split(string(storedPass), "$")
	saltB64 := parts[0]

	// Comparamos contraseñas
	if !verifyPassword(req.Password, string(storedPass)) {
		attempts++
		s.db.Put("login_attempts", []byte(req.Username), uint64ToBytes(attempts))
		s.db.Put("login_attempts_time", []byte(req.Username), uint64ToBytes(uint64(time.Now().Unix())))
		return api.Response{Success: false, Message: "Credenciales inválidas"}
	}

	// Limpiamos intentos de login
	s.db.Delete("login_attempts", []byte(req.Username))
	s.db.Delete("login_attempts_time", []byte(req.Username))

	// Generamos un nuevo token
	token, err := s.generateToken()
	if err != nil {
		return api.Response{Success: false, Message: "Error al generar token"}
	}

	session := Session{
		Token:     hashToken(token),
		ExpiresAt: time.Now().Add(24 * time.Hour),
	}

	sessionJSON, err := json.Marshal(session)
	if err != nil {
		return api.Response{Success: false, Message: "Error al crear sesión"}
	}

	if err := s.db.Put("sessions", []byte(req.Username), sessionJSON); err != nil {
		return api.Response{Success: false, Message: "Error al crear sesión"}
	}

	// Recuperamos el rol desde el bucket 'roles'
	role, err := s.db.Get("roles", []byte(req.Username))
	if err != nil {
		role = []byte("normal") // fallback en caso de error
	}

	return api.Response{
		Success: true,
		Message: "Login exitoso",
		Token:   token,
		Salt:    saltB64,
		Role:    string(role),
	}
}

// fetchData verifica el token y retorna el contenido del namespace 'userdata'.
func (s *server) fetchData(req api.Request) api.Response {
	// Chequeo de credenciales
	if req.Username == "" || req.Token == "" {
		return api.Response{Success: false, Message: "Faltan credenciales"}
	}
	if !s.isTokenValid(req.Username, req.Token) {
		return api.Response{Success: false, Message: "Token inválido o sesión expirada"}
	}

	// Obtenemos los datos asociados al usuario desde 'userdata'
	rawData, err := s.db.Get("userdata", []byte(req.Username))
	if err != nil {
		return api.Response{Success: false, Message: "Error al obtener datos del usuario"}
	}

	return api.Response{
		Success: true,
		Message: "Datos privados de " + req.Username,
		Data:    string(rawData),
	}
}

// updateData cambia el contenido de 'userdata' (los "datos" del usuario)
// después de validar el token.
func (s *server) updateData(req api.Request) api.Response {
	// Chequeo de credenciales
	if req.Username == "" || req.Token == "" {
		return api.Response{Success: false, Message: "Faltan credenciales"}
	}
	if !s.isTokenValid(req.Username, req.Token) {
		return api.Response{Success: false, Message: "Token inválido o sesión expirada"}
	}

	// Escribimos el nuevo dato en 'userdata'
	if err := s.db.Put("userdata", []byte(req.Username), []byte(req.Data)); err != nil {
		return api.Response{Success: false, Message: "Error al actualizar datos del usuario"}
	}

	return api.Response{Success: true, Message: "Datos de usuario actualizados"}
}

// logoutUser borra la sesión en 'sessions', invalidando el token.
func (s *server) logoutUser(req api.Request) api.Response {
	// Chequeo de credenciales
	if req.Username == "" || req.Token == "" {
		return api.Response{Success: false, Message: "Faltan credenciales"}
	}
	if !s.isTokenValid(req.Username, req.Token) {
		return api.Response{Success: false, Message: "Token inválido o sesión expirada"}
	}

	// Borramos la entrada en 'sessions'
	if err := s.db.Delete("sessions", []byte(req.Username)); err != nil {
		return api.Response{Success: false, Message: "Error al cerrar sesión"}
	}

	return api.Response{Success: true, Message: "Sesión cerrada correctamente"}
}

// userExists comprueba si existe un usuario con la clave 'username'
// en 'auth'. Si no se encuentra, retorna false.
func (s *server) userExists(username string) (bool, error) {
	_, err := s.db.Get("auth", []byte(username))
	if err != nil {
		// Si no existe namespace o la clave:
		if strings.Contains(err.Error(), "bucket no encontrado: auth") {
			return false, nil
		}
		if err.Error() == "clave no encontrada: "+username {
			return false, nil
		}
		return false, err
	}
	return true, nil
}

// isTokenValid comprueba que el token almacenado en 'sessions'
// coincida con el token proporcionado.
func (s *server) isTokenValid(username, token string) bool {
	sessionJSON, err := s.db.Get("sessions", []byte(username))
	if err != nil {
		return false
	}

	var session Session
	if err := json.Unmarshal(sessionJSON, &session); err != nil {
		return false
	}

	// Verificamos si el token ha expirado (24 horas)
	if time.Now().After(session.ExpiresAt) {
		s.db.Delete("sessions", []byte(username))
		return false
	}

	expectedHashToken := hashToken(token)

	return subtle.ConstantTimeCompare([]byte(session.Token), []byte(expectedHashToken)) == 1
}

// sendPrivateMessage guarda un mensaje cifrado para un destinatario.
func (s *server) sendPrivateMessage(req api.Request) api.Response {
	// 1. Validar credenciales
	if req.Username == "" || req.Token == "" {
		return api.Response{Success: false, Message: "Faltan credenciales"}
	}
	if !s.isTokenValid(req.Username, req.Token) {
		return api.Response{Success: false, Message: "Token inválido o sesión expirada"}
	}

	// 2. Validar campos necesarios
	if req.Recipient == "" || req.Encrypted == "" {
		return api.Response{Success: false, Message: "Faltan destinatario o mensaje"}
	}

	// 3. Componer clave única (por ejemplo, con timestamp)
	key := fmt.Sprintf("%s:%d", req.Recipient, s.generateTimestamp())

	// 4. Crear mensaje como JSON (se puede extender más adelante)
	messageData := fmt.Sprintf("%s|%s", req.Username, req.Encrypted) // formato "emisor|mensaje_cifrado"

	// 5. Guardar el mensaje en el namespace 'mensajes'
	if err := s.db.Put("mensajes", []byte(key), []byte(messageData)); err != nil {
		return api.Response{Success: false, Message: "Error al guardar mensaje"}
	}

	return api.Response{Success: true, Message: "Mensaje enviado correctamente"}
}

func (s *server) generateTimestamp() int64 {
	return atomic.AddInt64(&s.tokenCounter, 1)
}

func (s *server) getMessages(req api.Request) api.Response {
	// Validar credenciales
	if req.Username == "" || req.Token == "" {
		return api.Response{Success: false, Message: "Faltan credenciales"}
	}
	if !s.isTokenValid(req.Username, req.Token) {
		return api.Response{Success: false, Message: "Token inválido o sesión expirada"}
	}

	// Obtener todas las claves que comiencen por el nombre del usuario
	prefix := []byte(req.Username + ":")
	keys, err := s.db.KeysByPrefix("mensajes", prefix)
	if err != nil {
		return api.Response{Success: false, Message: "Error al acceder a mensajes"}
	}

	if len(keys) == 0 {
		return api.Response{Success: true, Message: "Sin mensajes", Data: ""}
	}

	var result strings.Builder

	for _, key := range keys {
		value, err := s.db.Get("mensajes", key)
		if err != nil {
			continue
		}

		// Separar emisor y contenido cifrado (recordemos formato: emisor|mensaje)
		parts := strings.SplitN(string(value), "|", 2)
		if len(parts) != 2 {
			continue
		}
		emisor := parts[0]
		contenido := parts[1]

		result.WriteString(fmt.Sprintf("De %s: %s\n", emisor, contenido))
	}

	return api.Response{
		Success: true,
		Message: "Mensajes recuperados",
		Data:    result.String(),
	}
}

func (s *server) createProposal(req api.Request) api.Response {
	// Validación básica
	if req.Username == "" || req.Token == "" {
		return api.Response{Success: false, Message: "Faltan credenciales"}
	}
	if !s.isTokenValid(req.Username, req.Token) {
		return api.Response{Success: false, Message: "Token inválido"}
	}

	if req.Data == "" || req.Extra == "" {
		return api.Response{Success: false, Message: "Faltan título o descripción"}
	}

	// Verificar si ya existe una propuesta con ese título
	exists, err := s.db.Get("propuestas", []byte(req.Data))
	if err == nil && exists != nil {
		return api.Response{Success: false, Message: "Ya existe una propuesta con ese título"}
	}

	// Guardamos la propuesta en el namespace "propuestas"
	if err := s.db.Put("propuestas", []byte(req.Data), []byte(req.Extra)); err != nil {
		return api.Response{Success: false, Message: "Error al guardar propuesta"}
	}

	return api.Response{Success: true, Message: "Propuesta creada correctamente"}
}

func (s *server) getProposals(req api.Request) api.Response {
	if req.Username == "" || req.Token == "" {
		return api.Response{Success: false, Message: "Faltan credenciales"}
	}
	if !s.isTokenValid(req.Username, req.Token) {
		return api.Response{Success: false, Message: "Token inválido"}
	}

	// Obtener todas las claves en el bucket "propuestas"
	keys, err := s.db.ListKeys("propuestas")
	if err != nil {
		return api.Response{Success: false, Message: "Error al acceder a propuestas"}
	}

	if len(keys) == 0 {
		return api.Response{Success: true, Message: "No hay propuestas disponibles", Data: ""}
	}

	// Creamos un mapa con título => JSON (como string)
	result := make(map[string]string)

	for _, key := range keys {
		value, err := s.db.Get("propuestas", key)
		if err != nil {
			continue
		}
		result[string(key)] = string(value)
	}

	// Convertimos el mapa a JSON
	jsonBytes, err := json.Marshal(result)
	if err != nil {
		return api.Response{Success: false, Message: "Error al generar respuesta"}
	}

	return api.Response{
		Success: true,
		Message: "Propuestas recuperadas",
		Data:    string(jsonBytes),
	}
}

func (s *server) voteProposal(req api.Request) api.Response {
	if req.Username == "" || req.Token == "" {
		return api.Response{Success: false, Message: "Faltan credenciales"}
	}
	if !s.isTokenValid(req.Username, req.Token) {
		return api.Response{Success: false, Message: "Token inválido"}
	}
	if req.Data == "" || req.Extra == "" {
		return api.Response{Success: false, Message: "Faltan datos del voto"}
	}

	// Generar ID de voto aleatorio
	id := make([]byte, 8)
	if _, err := rand.Read(id); err != nil {
		return api.Response{Success: false, Message: "Error al generar ID de voto"}
	}

	// Clave: votos:<título>:<id_random>
	key := fmt.Sprintf("votos:%s:%x", req.Data, id)

	// Verificamos si ya ha votado
	keyCheck := fmt.Sprintf("%s:%s", req.Username, req.Data)
	_, err := s.db.Get("voto_control", []byte(keyCheck))
	if err == nil {
		return api.Response{Success: false, Message: "Ya has votado en esta propuesta"}
	}

	// Guardamos el voto cifrado
	if err := s.db.Put("votaciones", []byte(key), []byte(req.Extra)); err != nil {
		return api.Response{Success: false, Message: "Error al guardar voto"}
	}

	//marcar como que ha votado
	_ = s.db.Put("voto_control", []byte(keyCheck), []byte("1"))

	return api.Response{Success: true, Message: "Voto registrado correctamente"}
}

func (s *server) createCategory(req api.Request) api.Response {
	if req.Username == "" || req.Token == "" {
		return api.Response{Success: false, Message: "Faltan credenciales"}
	}
	if !s.isTokenValid(req.Username, req.Token) {
		return api.Response{Success: false, Message: "Token inválido"}
	}
	if req.Data == "" {
		return api.Response{Success: false, Message: "Nombre de categoría vacío"}
	}

	exists, err := s.db.Get("categorias", []byte(req.Data))
	if err == nil && exists != nil {
		return api.Response{Success: false, Message: "La categoría ya existe"}
	}

	if err := s.db.Put("categorias", []byte(req.Data), []byte("1")); err != nil {
		return api.Response{Success: false, Message: "Error al guardar categoría"}
	}

	return api.Response{Success: true, Message: "Categoría creada correctamente"}
}

func (s *server) getVotes(req api.Request) api.Response {
	if req.Username == "" || req.Token == "" {
		return api.Response{Success: false, Message: "Faltan credenciales"}
	}
	if !s.isTokenValid(req.Username, req.Token) {
		return api.Response{Success: false, Message: "Token inválido"}
	}
	if req.Data == "" {
		return api.Response{Success: false, Message: "Falta el título de la propuesta"}
	}

	// Buscamos las claves que empiecen por "votos:<titulo>:"
	prefix := []byte(fmt.Sprintf("votos:%s:", req.Data))
	keys, err := s.db.KeysByPrefix("votaciones", prefix)
	if err != nil {
		return api.Response{Success: false, Message: "Error al buscar votos"}
	}

	var votes []string
	for _, key := range keys {
		val, err := s.db.Get("votaciones", key)
		if err != nil {
			continue
		}
		votes = append(votes, string(val))
	}

	jsonBytes, err := json.Marshal(votes)
	if err != nil {
		return api.Response{Success: false, Message: "Error al preparar respuesta"}
	}

	return api.Response{
		Success: true,
		Message: "Votos recuperados",
		Data:    string(jsonBytes),
	}
}

func (s *server) assignRole(req api.Request) api.Response {
	if req.Username == "" || req.Token == "" || req.Data == "" || req.Role == "" {
		return api.Response{Success: false, Message: "Faltan datos para asignar rol"}
	}
	if !s.isTokenValid(req.Username, req.Token) {
		return api.Response{Success: false, Message: "Token inválido"}
	}

	// Verificamos que el solicitante sea admin
	solicitanteRole, err := s.db.Get("roles", []byte(req.Username))
	if err != nil || string(solicitanteRole) != "admin" {
		return api.Response{Success: false, Message: "Solo los administradores pueden asignar roles"}
	}

	// Asignamos el nuevo rol al usuario objetivo
	if err := s.db.Put("roles", []byte(req.Data), []byte(req.Role)); err != nil {
		return api.Response{Success: false, Message: "Error al asignar rol"}
	}

	return api.Response{Success: true, Message: fmt.Sprintf("Rol '%s' asignado a %s", req.Role, req.Data)}
}

func (s *server) listUsers(req api.Request) api.Response {
	if req.Username == "" || req.Token == "" {
		return api.Response{Success: false, Message: "Faltan credenciales"}
	}
	if !s.isTokenValid(req.Username, req.Token) {
		return api.Response{Success: false, Message: "Token inválido"}
	}

	role, err := s.db.Get("roles", []byte(req.Username))
	if err != nil || string(role) != "admin" {
		return api.Response{Success: false, Message: "Solo los administradores pueden listar usuarios"}
	}

	keys, err := s.db.ListKeys("auth")
	if err != nil {
		return api.Response{Success: false, Message: "Error al acceder a usuarios"}
	}

	users := make(map[string]string)
	for _, key := range keys {
		r, _ := s.db.Get("roles", key)
		role := "normal"
		if r != nil {
			role = string(r)
		}
		users[string(key)] = role
	}

	jsonBytes, err := json.Marshal(users)
	if err != nil {
		return api.Response{Success: false, Message: "Error al generar listado"}
	}

	return api.Response{Success: true, Message: "Usuarios listados", Data: string(jsonBytes)}
}

func (s *server) deleteUserMessages(req api.Request) api.Response {
	if req.Username == "" || req.Token == "" || req.Data == "" {
		return api.Response{Success: false, Message: "Faltan credenciales o usuario objetivo"}
	}
	if !s.isTokenValid(req.Username, req.Token) {
		return api.Response{Success: false, Message: "Token inválido"}
	}

	role, err := s.db.Get("roles", []byte(req.Username))
	if err != nil || (string(role) != "admin" && string(role) != "moderador") {
		return api.Response{Success: false, Message: "No tienes permiso para esta operación"}
	}

	// Buscar mensajes cuyo destinatario comience con el nombre del usuario objetivo
	prefix := []byte(req.Data + ":")
	keys, err := s.db.KeysByPrefix("mensajes", prefix)
	if err != nil {
		return api.Response{Success: false, Message: "Error al buscar mensajes"}
	}

	// Eliminar cada mensaje
	for _, key := range keys {
		_ = s.db.Delete("mensajes", key)
	}

	return api.Response{Success: true, Message: "Mensajes del usuario eliminados"}
}

func initAdminUser(s *server) error {
	const adminUsername = "admin"
	const adminPassword = "admin"

	// Comprobamos si ya existe
	exists, err := s.userExists(adminUsername)
	if err != nil {
		return err
	}
	if exists {
		// Ya existe, no hacer nada
		return nil
	}

	// Hashear la contraseña
	hashed, err := hashPassword(adminPassword)
	if err != nil {
		return fmt.Errorf("error al hashear la contraseña del admin: %v", err)
	}

	// Guardar en 'auth'
	if err := s.db.Put("auth", []byte(adminUsername), []byte(hashed)); err != nil {
		return fmt.Errorf("error al guardar las credenciales del admin: %v", err)
	}

	// Crear espacio en 'userdata'
	if err := s.db.Put("userdata", []byte(adminUsername), []byte("")); err != nil {
		return fmt.Errorf("error al inicializar los datos del admin: %v", err)
	}

	// Asignar rol 'admin'
	if err := s.db.Put("roles", []byte(adminUsername), []byte("admin")); err != nil {
		return fmt.Errorf("error al asignar el rol admin: %v", err)
	}

	// Registrar clave pública vacía (si usas cifrado, puedes omitir esto o dejarlo vacío)
	if err := s.db.Put("pubkeys", []byte(adminUsername), []byte("")); err != nil {
		return fmt.Errorf("error al guardar la clave pública del admin: %v", err)
	}

	fmt.Println("✅ Usuario admin creado por primera vez (admin/admin)")
	return nil
}

func (s *server) getCategories(req api.Request) api.Response {
	if req.Username == "" || req.Token == "" {
		return api.Response{Success: false, Message: "Faltan credenciales"}
	}
	if !s.isTokenValid(req.Username, req.Token) {
		return api.Response{Success: false, Message: "Token inválido"}
	}

	keys, err := s.db.ListKeys("categorias")
	if err != nil {
		return api.Response{Success: false, Message: "Error al acceder a categorías"}
	}

	var categories []string
	for _, key := range keys {
		categories = append(categories, string(key))
	}

	jsonBytes, err := json.Marshal(categories)
	if err != nil {
		return api.Response{Success: false, Message: "Error al preparar categorías"}
	}

	return api.Response{
		Success: true,
		Message: "Categorías recuperadas",
		Data:    string(jsonBytes),
	}
}
