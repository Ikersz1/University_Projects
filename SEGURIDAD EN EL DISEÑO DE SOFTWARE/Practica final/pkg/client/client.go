// El paquete client contiene la lógica de interacción con el usuario
// así como de comunicación con el servidor.
package client

import (
	"bytes"
	"crypto/aes"
	"crypto/cipher"
	"crypto/rand"
	"crypto/tls"
	"encoding/base64"
	"encoding/json"
	"fmt"
	"io"
	"log"
	"net/http"
	"os"
	"strings"

	"golang.org/x/crypto/argon2"

	"prac/pkg/api"
	"prac/pkg/ui"
)

// client estructura interna no exportada que controla
// el estado de la sesión (usuario, token) y logger.
type client struct {
	log         *log.Logger
	currentUser string
	authToken   string
	currentRole string // NUEVO: rol del usuario
}

// Run es la única función exportada de este paquete.
// Crea un client interno y ejecuta el bucle principal.
func Run() {
	// Creamos un logger con prefijo 'cli' para identificar
	// los mensajes en la consola.
	c := &client{
		log: log.New(os.Stdout, "[cli] ", log.LstdFlags),
	}
	c.runLoop()
}

// runLoop maneja la lógica del menú principal.
// Si NO hay usuario logueado, se muestran ciertas opciones;
// si SÍ hay usuario logueado, se muestran otras.

func (c *client) runLoop() {
	for {
		ui.ClearScreen()

		// Título del menú
		var title string
		if c.currentUser == "" {
			title = "Menú"
		} else {
			title = fmt.Sprintf("Menú (%s - %s)", c.currentUser, c.currentRole)
		}

		// Opciones del menú
		var options []string

		if c.currentUser == "" {
			options = []string{
				"Registrar usuario",
				"Iniciar sesión",
				"Salir",
			}
		} else {
			// Común para todos los roles logueados
			options = []string{
				"Ver datos",
				"Actualizar datos",
				"Enviar mensaje privado",
				"Leer mensajes recibidos",
				"Crear propuesta",
				"Ver propuestas disponibles",
				"Votar una propuesta",
				"Ver resultados de una propuesta",
				"Crear nueva categoría",
			}

			// Acceso exclusivo para moderador y admin
			if c.currentRole == "moderador" || c.currentRole == "admin" {
				options = append(options, "Eliminar mensajes de usuario")
			}

			// Acceso exclusivo para admin
			if c.currentRole == "admin" {
				options = append(options, "Listar usuarios")
				options = append(options, "Asignar rol a usuario")
			}

			// Cierre de sesión y salida (siempre)
			options = append(options, "Cerrar sesión")
			options = append(options, "Salir")
		}

		// Mostrar menú
		choice := ui.PrintMenu(title, options)

		if c.currentUser == "" {
			switch choice {
			case 1:
				c.registerUser()
			case 2:
				c.loginUser()
			case 3:
				c.log.Println("Saliendo del cliente...")
				return
			}
		} else {
			index := 1
			if choice == index {
				c.fetchData()
			} else if choice == index+1 {
				c.updateData()
			} else if choice == index+2 {
				c.sendPrivateMessage()
			} else if choice == index+3 {
				c.readMessages()
			} else if choice == index+4 {
				c.createProposal()
			} else if choice == index+5 {
				c.viewProposals()
			} else if choice == index+6 {
				c.voteProposal()
			} else if choice == index+7 {
				c.viewResults()
			} else if choice == index+8 {
				c.createCategory()
			} else if c.currentRole == "moderador" || c.currentRole == "admin" {
				if choice == index+9 {
					c.deleteUserMessages()
				} else if c.currentRole == "admin" {
					if choice == index+10 {
						c.listUsers()
					} else if choice == index+11 {
						c.assignRole()
					} else if choice == index+12 {
						c.logoutUser()
					} else if choice == index+13 {
						c.log.Println("Saliendo del cliente...")
						return
					}
				} else { // moderador sin permisos admin
					if choice == index+10 {
						c.logoutUser()
					} else if choice == index+11 {
						c.log.Println("Saliendo del cliente...")
						return
					}
				}
			} else { // rol normal
				if choice == index+9 {
					c.logoutUser()
				} else if choice == index+10 {
					c.log.Println("Saliendo del cliente...")
					return
				}
			}
		}

		ui.Pause("Pulsa [Enter] para continuar...")
	}
}

func (c *client) registerUser() {
	ui.ClearScreen()
	fmt.Println("** Registro de usuario **")

	username := ui.ReadInput("Nombre de usuario")
	password := ui.ReadInput("Contraseña")

	// 1. Generar y guardar claves ECC
	err := SetupAndSaveKeyPair(username, password)
	if err != nil {
		fmt.Println("Error al generar claves ECC:", err)
		return
	}

	// 2. Leer la clave pública para enviarla
	kp, err := LoadKeyPairFromDisk(fmt.Sprintf("%s_keypair.json", username))
	if err != nil {
		fmt.Println("Error al leer clave pública:", err)
		return
	}

	// 3. Enviar al servidor la clave pública como Extra
	res := c.sendRequest(api.Request{
		Action:   api.ActionRegister,
		Username: username,
		Password: password,
		Extra:    kp.PublicKey, // clave pública en Extra
	})

	// 4. Mostrar resultado
	fmt.Println("Éxito:", res.Success)
	fmt.Println("Mensaje:", res.Message)

	if res.Success {
		c.log.Println("Registro exitoso; intentando login automático...")

		// 5. Login automático
		loginRes := c.sendRequest(api.Request{
			Action:   api.ActionLogin,
			Username: username,
			Password: password,
		})
		if loginRes.Success {
			c.currentUser = username
			c.authToken = loginRes.Token
			c.currentRole = loginRes.Role // ⬅️ Guarda también el rol recibido

			fmt.Println("Login automático exitoso. Token guardado.")
			fmt.Printf("Rol asignado: %s\n", c.currentRole)

			salt, err := base64.RawStdEncoding.DecodeString(loginRes.Salt)
			if err != nil {
				fmt.Println("Salt malformado:", err)
				return
			}
			key := argon2.IDKey([]byte(password), salt, 3, 64*1024, 4, 32)
			SetKey(key)
			fmt.Println("Sesión iniciada. Clave cifrado establecida.")
			fmt.Println("✅ Par de claves ECC listo para cifrado punto a punto.")
		} else {
			fmt.Println("No se ha podido hacer login automático:", loginRes.Message)
		}
	}
}

func (c *client) getPublicKeyOf(user string) (string, error) {
	req := api.Request{
		Action:    api.ActionGetPubKey,
		Username:  c.currentUser,
		Token:     c.authToken,
		Recipient: user,
	}

	res := c.sendRequest(req)
	if !res.Success {
		return "", fmt.Errorf("error al obtener clave pública: %s", res.Message)
	}

	return res.Data, nil // contiene la clave pública base64
}

// loginUser pide credenciales y realiza un login en el servidor.
func (c *client) loginUser() {
	ui.ClearScreen()
	fmt.Println("** Inicio de sesión **")

	username := ui.ReadInput("Nombre de usuario")
	password := ui.ReadInput("Contraseña")

	// Enviamos la solicitud de login
	res := c.sendRequest(api.Request{
		Action:   api.ActionLogin,
		Username: username,
		Password: password,
	})

	// Mostramos resultado
	fmt.Println("Éxito:", res.Success)
	fmt.Println("Mensaje:", res.Message)

	// Si el login fue exitoso, guardamos estado y clave
	if res.Success {
		c.currentUser = username
		c.authToken = res.Token
		c.currentRole = res.Role // ⬅️ Guardamos el rol devuelto por el servidor

		fmt.Println("Sesión iniciada con éxito.")
		fmt.Printf("Token guardado. Rol asignado: %s\n", c.currentRole)

		// Decodificar salt y derivar clave simétrica para cifrado
		salt, err := base64.RawStdEncoding.DecodeString(res.Salt)
		if err != nil {
			fmt.Println("Salt malformado:", err)
			return
		}
		key := argon2.IDKey([]byte(password), salt, 3, 64*1024, 4, 32)
		SetKey(key)

		fmt.Println("Clave de cifrado establecida correctamente.")
	}
}

// fetchData pide datos privados al servidor.
// El servidor devuelve la data asociada al usuario logueado.
func (c *client) fetchData() {
	ui.ClearScreen()
	fmt.Println("** Obtener datos del usuario **")

	// Chequeo básico de que haya sesión
	if c.currentUser == "" || c.authToken == "" {
		fmt.Println("No estás logueado. Inicia sesión primero.")
		return
	}

	// Hacemos la request con ActionFetchData
	res := c.sendRequest(api.Request{
		Action:   api.ActionFetchData,
		Username: c.currentUser,
		Token:    c.authToken,
	})

	fmt.Println("Éxito:", res.Success)
	fmt.Println("Mensaje:", res.Message)

	// Si fue exitoso, mostramos la data recibida
	if res.Success {
		fmt.Println("Tus datos:", res.Data)
	}
}

// updateData pide nuevo texto y lo envía al servidor con ActionUpdateData.
func (c *client) updateData() {
	ui.ClearScreen()
	fmt.Println("** Actualizar datos del usuario **")

	if c.currentUser == "" || c.authToken == "" {
		fmt.Println("No estás logueado. Inicia sesión primero.")
		return
	}

	// Leemos la nueva Data
	newData := ui.ReadInput("Introduce el contenido que desees almacenar")

	// Enviamos la solicitud de actualización
	res := c.sendRequest(api.Request{
		Action:   api.ActionUpdateData,
		Username: c.currentUser,
		Token:    c.authToken,
		Data:     newData,
	})

	fmt.Println("Éxito:", res.Success)
	fmt.Println("Mensaje:", res.Message)
}

// logoutUser llama a la acción logout en el servidor, y si es exitosa,
// borra la sesión local (currentUser/authToken).
func (c *client) logoutUser() {
	ui.ClearScreen()
	fmt.Println("** Cerrar sesión **")

	if c.currentUser == "" || c.authToken == "" {
		fmt.Println("No estás logueado.")
		return
	}

	// Enviamos la solicitud de logout al servidor
	res := c.sendRequest(api.Request{
		Action:   api.ActionLogout,
		Username: c.currentUser,
		Token:    c.authToken,
	})

	// Limpiar clave de cifrado local
	ClearKey()

	fmt.Println("Éxito:", res.Success)
	fmt.Println("Mensaje:", res.Message)

	// Si fue exitoso, limpiamos todos los datos de sesión local
	if res.Success {
		c.currentUser = ""
		c.authToken = ""
		c.currentRole = "" // ⬅️ limpiamos también el rol
	}
}

// sendRequest envía un POST JSON a la URL del servidor y devuelve la respuesta decodificada.
func (c *client) sendRequest(req api.Request) api.Response {
	jsonData, _ := json.Marshal(req)
	// Configurar un cliente HTTP con TLS que omita la verificación del certificado.
	httpsClient := &http.Client{
		Transport: &http.Transport{
			TLSClientConfig: &tls.Config{InsecureSkipVerify: true},
		},
	}

	// Cambiamos la URL para usar HTTPS y el puerto 8443 en vez del 8080 que estaba antes.
	resp, err := httpsClient.Post("https://localhost:8443/api", "application/json", bytes.NewBuffer(jsonData))
	if err != nil {
		fmt.Println("Error al contactar con el servidor:", err)
		return api.Response{Success: false, Message: "Error de conexión"}
	}
	defer resp.Body.Close()

	body, _ := io.ReadAll(resp.Body)
	var res api.Response
	_ = json.Unmarshal(body, &res)
	return res
}

// A PARTIR DE AQUI, EL CÓDIGO FUENTE ES NUESTRO

func (c *client) sendPrivateMessage() {
	ui.ClearScreen()
	fmt.Println("** Enviar mensaje privado **")

	if c.currentUser == "" || c.authToken == "" {
		fmt.Println("No estás logueado.")
		return
	}

	recipient := ui.ReadInput("Nombre del destinatario")
	message := ui.ReadInput("Mensaje a enviar")
	password := ui.ReadInput("Tu contraseña (para descifrar clave privada)")

	// 1. Cargar clave privada local
	privKey, err := LoadPrivateKeyFromDisk(c.currentUser, password)
	if err != nil {
		fmt.Println("Error al cargar tu clave privada:", err)
		return
	}

	// 2. Obtener clave pública del destinatario desde el servidor
	res := c.sendRequest(api.Request{
		Action:   api.ActionUploadPubKey,
		Username: c.currentUser,
		Token:    c.authToken,
		Data:     recipient,
	})

	if !res.Success || res.Data == "" {
		fmt.Println("Error al obtener la clave pública del destinatario:", res.Message)
		return
	}
	recipientPubKey := res.Data

	// 3. Derivar clave simétrica compartida con ECDH
	sharedKey, err := DeriveSharedKey(privKey, recipientPubKey)
	if err != nil {
		fmt.Println("Error derivando clave simétrica:", err)
		return
	}

	// 4. Cifrar el mensaje con AES-GCM usando sharedKey
	block, err := aes.NewCipher(sharedKey)
	if err != nil {
		fmt.Println("Error al crear cipher:", err)
		return
	}
	gcm, err := cipher.NewGCM(block)
	if err != nil {
		fmt.Println("Error al crear GCM:", err)
		return
	}
	nonce := make([]byte, gcm.NonceSize())
	if _, err := io.ReadFull(rand.Reader, nonce); err != nil {
		fmt.Println("Error generando nonce:", err)
		return
	}
	ciphertext := gcm.Seal(nil, nonce, []byte(message), nil)
	fullMsg := append(nonce, ciphertext...)
	encodedMsg := base64.StdEncoding.EncodeToString(fullMsg)

	// 5. Enviar el mensaje al servidor
	req := api.Request{
		Action:    api.ActionSendMessage,
		Username:  c.currentUser,
		Token:     c.authToken,
		Recipient: recipient,
		Encrypted: encodedMsg,
	}

	response := c.sendRequest(req)
	fmt.Println("✅ Éxito:", response.Success)
	fmt.Println("📨 Mensaje del servidor:", response.Message)
}

func (c *client) readMessages() {
	ui.ClearScreen()
	fmt.Println("** Mensajes recibidos **")

	if c.currentUser == "" || c.authToken == "" {
		fmt.Println("No estás logueado.")
		return
	}

	password := ui.ReadInput("Tu contraseña (para descifrar tu clave privada)")

	// Cargamos la clave privada desde disco
	privKey, err := LoadPrivateKeyFromDisk(c.currentUser, password)
	if err != nil {
		fmt.Println("Error al cargar tu clave privada:", err)
		return
	}

	// Petición para obtener los mensajes
	req := api.Request{
		Action:   api.ActionGetMessages,
		Username: c.currentUser,
		Token:    c.authToken,
	}
	res := c.sendRequest(req)

	if !res.Success || res.Data == "" {
		fmt.Println("Error:", res.Message)
		return
	}

	fmt.Println("Mensajes recibidos:\n")

	lines := strings.Split(res.Data, "\n")
	for _, line := range lines {
		if strings.HasPrefix(line, "De ") {
			parts := strings.SplitN(line, ": ", 2)
			if len(parts) != 2 {
				continue
			}
			emisor := strings.TrimPrefix(parts[0], "De ")
			cifrado := parts[1]

			// Obtener clave pública del emisor
			pubkeyRes := c.sendRequest(api.Request{
				Action:   api.ActionUploadPubKey,
				Username: c.currentUser,
				Token:    c.authToken,
				Data:     emisor,
			})
			if !pubkeyRes.Success || pubkeyRes.Data == "" {
				fmt.Printf("De %s: [clave pública no disponible]\n", emisor)
				continue
			}

			sharedKey, err := DeriveSharedKey(privKey, pubkeyRes.Data)
			if err != nil {
				fmt.Printf("De %s: [error derivando clave]\n", emisor)
				continue
			}

			// Descifrar con AES-GCM
			cipherdata, err := base64.StdEncoding.DecodeString(cifrado)
			if err != nil {
				fmt.Printf("De %s: [mensaje corrupto]\n", emisor)
				continue
			}

			block, err := aes.NewCipher(sharedKey)
			if err != nil {
				fmt.Printf("De %s: [cipher inválido]\n", emisor)
				continue
			}
			gcm, err := cipher.NewGCM(block)
			if err != nil {
				fmt.Printf("De %s: [GCM inválido]\n", emisor)
				continue
			}
			if len(cipherdata) < gcm.NonceSize() {
				fmt.Printf("De %s: [nonce inválido]\n", emisor)
				continue
			}

			nonce, ct := cipherdata[:gcm.NonceSize()], cipherdata[gcm.NonceSize():]
			plain, err := gcm.Open(nil, nonce, ct, nil)
			if err != nil {
				fmt.Printf("De %s: [error al descifrar]\n", emisor)
				continue
			}

			fmt.Printf("De %s: %s\n", emisor, string(plain))
		}
	}
}
func (c *client) createProposal() {
	ui.ClearScreen()
	fmt.Println("** Crear nueva propuesta **")

	if c.currentUser == "" || c.authToken == "" {
		fmt.Println("Debes iniciar sesión para crear una propuesta.")
		return
	}

	// 1. Pedimos las categorías al servidor
	catRes := c.sendRequest(api.Request{
		Action:   api.ActionGetCategories, // Necesitamos añadir esta acción en el servidor
		Username: c.currentUser,
		Token:    c.authToken,
	})

	if !catRes.Success || catRes.Data == "" {
		fmt.Println("Error al obtener categorías:", catRes.Message)
		return
	}

	var categories []string
	if err := json.Unmarshal([]byte(catRes.Data), &categories); err != nil {
		fmt.Println("Error interpretando categorías:", err)
		return
	}

	if len(categories) == 0 {
		fmt.Println("No hay categorías disponibles. Crea una primero.")
		return
	}

	fmt.Println("Categorías disponibles:")
	for i, cat := range categories {
		fmt.Printf("  %d. %s\n", i+1, cat)
	}

	// 2. Seleccionar una categoría
	var catIndex int
	fmt.Print("Selecciona una categoría (número): ")
	fmt.Scanln(&catIndex)

	if catIndex < 1 || catIndex > len(categories) {
		fmt.Println("Selección inválida.")
		return
	}
	selectedCategory := categories[catIndex-1]

	// 3. Resto de la propuesta
	title := ui.ReadInput("Título de la propuesta")
	description := ui.ReadInput("Descripción de la propuesta")

	var numOptions int
	fmt.Print("¿Cuántas opciones tendrá la propuesta?: ")
	_, err := fmt.Scanln(&numOptions)
	if err != nil || numOptions <= 0 {
		fmt.Println("Número de opciones inválido.")
		return
	}

	var options []string
	for i := 1; i <= numOptions; i++ {
		option := ui.ReadInput(fmt.Sprintf("Introduce la opción %d", i))
		options = append(options, option)
	}

	// 4. JSON con categoría incluida
	proposal := map[string]interface{}{
		"categoria":   selectedCategory,
		"descripcion": description,
		"opciones":    options,
	}
	jsonBytes, err := json.Marshal(proposal)
	if err != nil {
		fmt.Println("Error al generar JSON:", err)
		return
	}

	req := api.Request{
		Action:   api.ActionCreateProposal,
		Username: c.currentUser,
		Token:    c.authToken,
		Data:     title,
		Extra:    string(jsonBytes),
	}

	res := c.sendRequest(req)

	fmt.Println("Éxito:", res.Success)
	fmt.Println("Mensaje:", res.Message)
}

func (c *client) viewProposals() {
	ui.ClearScreen()
	fmt.Println("** Propuestas disponibles **")

	if c.currentUser == "" || c.authToken == "" {
		fmt.Println("No estás logueado.")
		return
	}

	// Preguntamos si quiere filtrar por categoría
	fmt.Println("¿Deseas filtrar por categoría?")
	fmt.Println("1. Sí")
	fmt.Println("2. No (ver todas)")
	choice := ui.ReadInt("Elige una opción")

	var category string
	if choice == 1 {
		// Pedimos lista de categorías
		res := c.sendRequest(api.Request{
			Action:   "get_categories",
			Username: c.currentUser,
			Token:    c.authToken,
		})

		if !res.Success {
			fmt.Println("Error al obtener categorías:", res.Message)
			return
		}

		var categories []string
		err := json.Unmarshal([]byte(res.Data), &categories)
		if err != nil {
			fmt.Println("Error al interpretar categorías:", err)
			return
		}

		if len(categories) == 0 {
			fmt.Println("No hay categorías disponibles.")
			return
		}

		fmt.Println("Categorías disponibles:")
		for i, cat := range categories {
			fmt.Printf("  %d. %s\n", i+1, cat)
		}

		catChoice := ui.ReadInt("Elige una categoría (número)")
		if catChoice < 1 || catChoice > len(categories) {
			fmt.Println("Selección inválida.")
			return
		}
		category = categories[catChoice-1]
	}

	// Petición para obtener propuestas
	req := api.Request{
		Action:   api.ActionGetProposals,
		Username: c.currentUser,
		Token:    c.authToken,
	}

	res := c.sendRequest(req)

	if !res.Success {
		fmt.Println("Error:", res.Message)
		return
	}

	if res.Data == "" {
		fmt.Println("No hay propuestas registradas.")
		return
	}

	// Parseamos las propuestas
	var proposals map[string]string
	err := json.Unmarshal([]byte(res.Data), &proposals)
	if err != nil {
		fmt.Println("Error al interpretar propuestas:", err)
		return
	}

	// Recorremos y mostramos propuestas (filtradas o no)
	for title, raw := range proposals {
		var p map[string]interface{}
		_ = json.Unmarshal([]byte(raw), &p)

		cat := p["categoria"]
		if category != "" && cat != category {
			continue // filtramos
		}

		fmt.Printf("\n📌 %s\n", title)
		if catStr, ok := cat.(string); ok {
			fmt.Println("  Categoría:", catStr)
		}
		if desc, ok := p["descripcion"].(string); ok {
			fmt.Println("  Descripción:", desc)
		}
		if opts, ok := p["opciones"].([]interface{}); ok {
			fmt.Println("  Opciones:")
			for i, opt := range opts {
				fmt.Printf("   %d. %v\n", i+1, opt)
			}
		}
	}
}

func (c *client) voteProposal() {
	ui.ClearScreen()
	fmt.Println("** Votar una propuesta **")

	if c.currentUser == "" || c.authToken == "" {
		fmt.Println("Debes iniciar sesión.")
		return
	}

	// Pedimos las propuestas al servidor
	req := api.Request{
		Action:   api.ActionGetProposals,
		Username: c.currentUser,
		Token:    c.authToken,
	}
	res := c.sendRequest(req)

	if !res.Success || res.Data == "" {
		fmt.Println("No se pudieron obtener propuestas:", res.Message)
		return
	}

	// Parseamos las propuestas
	var proposals map[string]string
	err := json.Unmarshal([]byte(res.Data), &proposals)
	if err != nil {
		fmt.Println("Error al interpretar propuestas:", err)
		return
	}

	// Mostramos las propuestas disponibles
	titles := make([]string, 0, len(proposals))
	fmt.Println("Propuestas disponibles:")
	i := 1
	for title := range proposals {
		fmt.Printf("  %d. %s\n", i, title)
		titles = append(titles, title)
		i++
	}

	// Elegir una propuesta por número
	var choice int
	fmt.Print("Introduce el número de la propuesta que quieres votar: ")
	fmt.Scanln(&choice)
	if choice < 1 || choice > len(titles) {
		fmt.Println("Selección inválida.")
		return
	}

	selected := titles[choice-1]
	raw := proposals[selected]

	// Mostramos sus opciones
	var p map[string]interface{}
	_ = json.Unmarshal([]byte(raw), &p)

	opts, ok := p["opciones"].([]interface{})
	if !ok {
		fmt.Println("La propuesta no tiene opciones válidas.")
		return
	}

	fmt.Printf("Opciones para '%s':\n", selected)
	for i, opt := range opts {
		fmt.Printf("  %d. %v\n", i+1, opt)
	}

	// Elige una opción
	fmt.Print("Introduce el número de tu voto: ")
	var voteIndex int
	fmt.Scanln(&voteIndex)
	if voteIndex < 1 || voteIndex > len(opts) {
		fmt.Println("Opción inválida.")
		return
	}

	// Obtenemos la opción elegida
	voteText := fmt.Sprintf("%v", opts[voteIndex-1])

	// // Ciframos el voto
	// encrypted, err := EncryptMessage(voteText)
	// if err != nil {
	// 	fmt.Println("Error al cifrar el voto:", err)
	// 	return
	// }

	// Enviamos el voto cifrado
	voteReq := api.Request{
		Action:   api.ActionVote,
		Username: c.currentUser,
		Token:    c.authToken,
		Data:     selected, // título de la propuesta
		Extra:    voteText,
	}

	voteRes := c.sendRequest(voteReq)
	fmt.Println("Éxito:", voteRes.Success)
	fmt.Println("Mensaje:", voteRes.Message)
}

func (c *client) createCategory() {
	ui.ClearScreen()
	fmt.Println("** Crear nueva categoría **")

	if c.currentUser == "" || c.authToken == "" {
		fmt.Println("Debes iniciar sesión.")
		return
	}

	name := ui.ReadInput("Nombre de la nueva categoría")

	req := api.Request{
		Action:   api.ActionCreateCategory,
		Username: c.currentUser,
		Token:    c.authToken,
		Data:     name,
	}

	res := c.sendRequest(req)

	fmt.Println("Éxito:", res.Success)
	fmt.Println("Mensaje:", res.Message)
}

func (c *client) viewResults() {
	ui.ClearScreen()
	fmt.Println("** Resultados de propuesta **")

	if c.currentUser == "" || c.authToken == "" {
		fmt.Println("No estás logueado.")
		return
	}

	// Pedimos las propuestas al servidor
	req := api.Request{
		Action:   api.ActionGetProposals,
		Username: c.currentUser,
		Token:    c.authToken,
	}
	res := c.sendRequest(req)

	if !res.Success || res.Data == "" {
		fmt.Println("No se pudieron obtener propuestas:", res.Message)
		return
	}

	var proposals map[string]string
	err := json.Unmarshal([]byte(res.Data), &proposals)
	if err != nil {
		fmt.Println("Error al interpretar propuestas:", err)
		return
	}

	// Mostramos las propuestas
	titles := make([]string, 0, len(proposals))
	fmt.Println("Propuestas disponibles:")
	i := 1
	for title := range proposals {
		fmt.Printf("  %d. %s\n", i, title)
		titles = append(titles, title)
		i++
	}

	// Elegir una propuesta
	var choice int
	fmt.Print("Introduce el número de la propuesta: ")
	fmt.Scanln(&choice)
	if choice < 1 || choice > len(titles) {
		fmt.Println("Selección inválida.")
		return
	}
	selected := titles[choice-1]

	// Enviamos petición para obtener los votos de esa propuesta
	voteReq := api.Request{
		Action:   api.ActionGetVotes,
		Username: c.currentUser,
		Token:    c.authToken,
		Data:     selected, // título de la propuesta
	}
	voteRes := c.sendRequest(voteReq)
	if !voteRes.Success || voteRes.Data == "" {
		fmt.Println("Error:", voteRes.Message)
		return
	}

	var votes []string
	if err := json.Unmarshal([]byte(voteRes.Data), &votes); err != nil {
		fmt.Println("Error al interpretar los votos:", err)
		return
	}
	if len(votes) == 0 {
		fmt.Println("No hay votos registrados aún.")
		return
	}

	// Contamos los votos que tiene cada opción
	counts := make(map[string]int)
	for _, option := range votes {
		counts[option]++
	}

	fmt.Println("\n Resultados de la propuesta:", selected)
	for option, count := range counts {
		fmt.Printf("  %s: %d votos\n", option, count)
	}
}

func (c *client) deleteUserMessages() {
	ui.ClearScreen()
	fmt.Println("** Eliminar mensajes de un usuario **")

	username := ui.ReadInput("¿A qué usuario deseas eliminar los mensajes?")
	req := api.Request{
		Action:   api.ActionDeleteUserMessages,
		Username: c.currentUser,
		Token:    c.authToken,
		Data:     username,
	}
	res := c.sendRequest(req)
	fmt.Println("Éxito:", res.Success)
	fmt.Println("Mensaje:", res.Message)
}

func (c *client) listUsers() {
	ui.ClearScreen()
	fmt.Println("** Lista de usuarios registrados **")

	req := api.Request{
		Action:   api.ActionListUsers,
		Username: c.currentUser,
		Token:    c.authToken,
	}
	res := c.sendRequest(req)
	if !res.Success {
		fmt.Println("Error:", res.Message)
		return
	}

	var users map[string]string
	if err := json.Unmarshal([]byte(res.Data), &users); err != nil {
		fmt.Println("Error al interpretar usuarios:", err)
		return
	}

	for user, role := range users {
		fmt.Printf("👤 %s - Rol: %s\n", user, role)
	}
}

func (c *client) assignRole() {
	ui.ClearScreen()
	fmt.Println("** Asignar rol a usuario **")

	username := ui.ReadInput("Nombre del usuario")
	role := ui.ReadInput("Nuevo rol (admin, moderador, normal)")

	req := api.Request{
		Action:   api.ActionAssignRole,
		Username: c.currentUser,
		Token:    c.authToken,
		Data:     username,
		Role:     role,
	}
	res := c.sendRequest(req)
	fmt.Println("Éxito:", res.Success)
	fmt.Println("Mensaje:", res.Message)
}
