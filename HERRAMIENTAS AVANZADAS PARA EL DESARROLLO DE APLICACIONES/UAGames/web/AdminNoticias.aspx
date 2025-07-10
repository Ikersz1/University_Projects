<%@ Page Title="" Language="C#" MasterPageFile="~/Site1.Master" AutoEventWireup="true" CodeBehind="AdminNoticias.aspx.cs" Inherits="web.AdminNoticias" %>
<asp:Content ID="Content1" ContentPlaceHolderID="head" runat="server">
</asp:Content>
<asp:Content ID="Content2" ContentPlaceHolderID="main" runat="server">
      <!DOCTYPE html>

<html xmlns="http://www.w3.org/1999/xhtml%22%3E>

    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Crear Productora</title>
    <style>
        .list {
            display: flex;
            flex-direction: column;
            align-items: center;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .list-img img {
            max-width: 100%;
            height: auto;
            border-radius: 5px;
            margin-bottom: 10px;
        }

        .list-content {
            text-align: center;
        }

        .list-content h1 {
            font-size: 36px;
            font-weight: bold;
            margin-bottom: 10px;
        }

        .list-content p.date {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .list-content p.content {
            font-size: 16px;
            line-height: 1.5;
            margin-bottom: 20px;
            white-space: pre-line;
        }
    </style>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .form-container {
            max-width: 500px;
            margin: auto;
            padding: 20px;
            background-color: #f2f2f2;
            border-radius: 5px;
        }
        label {
            display: inline-block;
            width: 100px;
            margin-bottom: 10px;
        }
        input[type=text], textarea {
            padding: 10px;
            width: 100%;
            border: none;
            border-radius: 3px;
            background-color: #eaeaea;
            margin-bottom: 20px;
            resize: vertical;
        }
        input[type=submit] {
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
        }

    </style>
    <div class="container bg-light overflow-auto list-container" style="min-height:300px;">

   
    <form id="form1">
        <div class="form-container">
            <h2>Crear Noticias</h2>
            <div>
                <label for="txtId">Id:</label>
                <asp:TextBox ID="txtId" runat="server" ></asp:TextBox>
            </div>
            <div>
                <label for="txtImg">Imagen:</label>
                <asp:TextBox ID="txtImg" runat="server"></asp:TextBox>
            </div>
            <div>
                <label for="txtTitulo">Titulo:</label>
                <asp:TextBox ID="txtTitulo" runat="server"></asp:TextBox>
            </div>
            <div>
                <label for="txtContenido">Contenido:</label>
                <asp:TextBox ID="txtContenido" runat="server" TextMode="MultiLine" Rows="4"></asp:TextBox>
            </div>
             <div>
                  <div>
                <label for="txtprodID">ID de la productora:</label>
                <asp:TextBox ID="txtprodID" runat="server" TextMode="MultiLine" Rows="4"></asp:TextBox>
            </div>
                <label for="txtFecha">Fecha de publicación:</label>
                <asp:TextBox ID="txtFecha" runat="server" TextMode="MultiLine" Rows="4"></asp:TextBox>
            </div>
<div>
    <asp:Button ID="btnCrearNoticia" runat="server" Text="Crear Noticia" OnClick="onCrear" style="background-color: green;" />
    <asp:Button ID="btnLeerNoticia" runat="server" Text="Leer Noticia por título" OnClick="onLeerTitulo" style="background-color: green;" />
    <asp:Button ID="btnLeerID" runat="server" Text="Leer Noticia por ID" OnClick="onLeerId" style="background-color: green;" />
    <asp:Button ID="btnBorrarNoticia" runat="server" Text="Borrar Noticia" OnClick="onBorrar" style="background-color: green;" />
    <asp:Button ID="btnUpdateNoticia" runat="server" Text="Actualizar Noticia" OnClick="onUpdate" style="background-color: green;" />
    <asp:Button ID="btnLeerTodas" runat="server" Text="Mostrar todas las noticias" OnClick="onLeerTodas" style="background-color: green;" />
</div>

        </div>
    </form>
    <br />
            <asp:Label ID="PResultado" runat="server" Text =" " />&nbsp;
            <br />
    <asp:ListView ID="ListView1" runat="server">
        <ItemTemplate>
            <div class="list">
                <div class="list-img">
                    <img src='<%#Eval("imagen") %>' />
                </div>
                <div class="list-content">
                    <h1><%#Eval("titulo") %></h1>
                    <p class="date"><%#Eval("fecha_public") %></p>
                    <p class="content"><%#Eval("contenido") %></p>
                </div>
            </div>
        </ItemTemplate>
       
    </asp:ListView>
         </div>
    <style>
    body {
        font-family: Arial, sans-serif;
    }

    .form-container {
        max-width: 500px;
        margin: auto;
        padding: 20px;
        background-color: #f2f2f2;
        border-radius: 5px;
    }

    label {
        display: inline-block;
        width: 100px;
        margin-bottom: 10px;
    }

    input[type=text],
    textarea {
        padding: 10px;
        width: 100%;
        border: none;
        border-radius: 3px;
        background-color: #eaeaea;
        margin-bottom: 20px;
        resize: vertical;
    }

    input[type=submit] {
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 3px;
        cursor: pointer;
        margin-right: 10px;
        margin-bottom: 10px;
    }

    .list {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        margin-bottom: 20px;
    }

    .list-img {
        margin-right: 10px;
        margin-bottom: 10px;
    }

    .list-img img {
        max-width: 100%;
        height: auto;
    }

    .list-content {
        text-align: left;
    }

    .list-button {
        margin-top: 10px;
    }

    .noticia-enlace {
        font-size: 18px;
        font-weight: bold;
        color: #0080FF;
        text-decoration: none;
    }
</style>



</html>

</asp:Content>