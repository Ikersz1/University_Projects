// El paquete api contiene las estructuras necesarias
// para la comunicación entre servidor y cliente.
package api

const (
	ActionRegister           = "register"
	ActionLogin              = "login"
	ActionFetchData          = "fetchData"
	ActionUpdateData         = "updateData"
	ActionLogout             = "logout"
	ActionSendMessage        = "send_message"
	ActionGetMessages        = "get_messages"
	ActionCreateProposal     = "create_proposal"
	ActionGetProposals       = "get_proposals"
	ActionVote               = "vote"
	ActionGetVotes           = "get_votes"
	ActionCreateCategory     = "create_category"
	ActionUploadPubKey       = "upload_pubkey"
	ActionGetPubKey          = "get_pubkey"
	ActionListUsers          = "list_users"
	ActionDeleteUserMessages = "delete_user_messages"
	ActionAssignRole         = "assign_role"
	ActionGetCategories      = "get_categories"
)

// Request y Response como antes
type Request struct {
	Action    string `json:"action"`
	Username  string `json:"username"`
	Password  string `json:"password,omitempty"`
	Token     string `json:"token,omitempty"`
	Data      string `json:"data,omitempty"`
	Recipient string `json:"recipient,omitempty"`
	Encrypted string `json:"encrypted,omitempty"`
	Extra     string `json:"extra,omitempty"` // Descripción de la propuesta
	PublicKey string `json:"public_key,omitempty"`
	Role      string `json:"role,omitempty"` // usado por el servidor para asignar/consultar roles

}

type Response struct {
	Success bool   `json:"success"`
	Message string `json:"message"`
	Token   string `json:"token,omitempty"`
	Data    string `json:"data,omitempty"`
	Salt    string `json:"salt,omitempty"`
	Role    string `json:"role,omitempty"` // devuelto al hacer login

}
