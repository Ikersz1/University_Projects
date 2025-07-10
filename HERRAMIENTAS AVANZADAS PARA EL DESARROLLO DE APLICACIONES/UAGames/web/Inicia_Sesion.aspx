<%@ Page Title="" Language="C#" UnobtrusiveValidationMode="None" MasterPageFile="~/Site1.Master" AutoEventWireup="true" CodeBehind="Inicia_Sesion.aspx.cs" Inherits="web.Inicia_Sesion" %>
<asp:Content ID="Content1" ContentPlaceHolderID="head" runat="server">
    <style>
        .container {
            background-color: #FFFFFF; /* Color blanco opcional para el fondo */
            padding: 20px;
            min-height: 625px;
        }
        
        h1 {
            color: #0000FF; /* Color azul para los títulos */
            margin-bottom: 20px;
        }
        
        .registro_inicio {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }
        
        .registro_inicio label {
            font-weight: bold;
            margin-bottom: 5px;
        }
        
        .registro_inicio input[type="text"],
        .registro_inicio input[type="password"] {
            border: 1px solid #0000FF; /* Borde azul para las cajas de texto */
            border-radius: 5px; /* Bordes redondeados para las cajas de texto */
            padding: 5px;
            margin-bottom: 10px;
        }
        
        .registro_inicio input[type="text"]:focus,
        .registro_inicio input[type="password"]:focus {
            outline: none;
            box-shadow: 0 0 5px #0000FF; /* Sombra azul al enfocar las cajas de texto */
        }
        
        .registro_inicio .btn {
            background-color: #0000FF; /* Color azul para el botón */
            color: #FFFFFF; /* Texto blanco para el botón */
            border-radius: 5px; /* Bordes redondeados para el botón */
            padding: 10px 20px;
            cursor: pointer;
        }
        
        .registro_inicio .btn:hover {
            background-color: #0000AA; /* Color azul oscuro al pasar el mouse por encima */
        }
    </style>
</asp:Content>
<asp:Content ID="Content2" ContentPlaceHolderID="main" runat="server">
    <div class="container bg-light overflow-auto">
        <div class="registro_inicio">
            <h1>Iniciar sesión</h1>
            <label for="TNick">Nick:</label>
            <asp:TextBox ID="TNick" runat="server"></asp:TextBox> 
            <asp:RequiredFieldValidator ID="RequiredNick" Enabled="false" runat="server" ErrorMessage=" * Usuario obligatorio" ControlToValidate="TNick">
            </asp:RequiredFieldValidator>
            <br />
            <label for="TPassword">Password:</label>
            <asp:TextBox ID="TPassword" runat="server" type="password"></asp:TextBox>
            <asp:RequiredFieldValidator ID="RequiredPassword" Enabled="false" runat="server" ErrorMessage=" * Contraseña obligatoria" ControlToValidate="TPassword">
            </asp:RequiredFieldValidator>
            <br />
            <br />
            <asp:Button ID="BBuscar" CssClass="btn btn-primary" runat="server" Text="Entrar" OnClick="Leer"/>
            <br />
            <asp:Label ID="LResultado" runat="server" Text =" " />
        </div>
   
</div></asp:Content>