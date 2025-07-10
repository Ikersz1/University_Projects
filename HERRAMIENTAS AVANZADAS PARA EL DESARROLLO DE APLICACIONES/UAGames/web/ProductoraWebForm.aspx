<%@ Page Title="" Language="C#" MasterPageFile="~/Site1.Master" AutoEventWireup="true" CodeBehind="ProductoraWebForm.aspx.cs" Inherits="web.ProductoraWebForm" %>

<asp:Content ID="Content1" ContentPlaceHolderID="head" runat="server">
</asp:Content>
<asp:Content ID="Content2" ContentPlaceHolderID="main" runat="server">
   <!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>Crear Productora</title>
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


    .container {
        background-color: #f2f2f2;
        border-radius: 5px;
        padding: 20px;
        margin-bottom: 20px;
    }

    .list-img {
        text-align: center;
        margin-bottom: 10px;
    }

    .list-img img {
        display: block;
        max-width: 100%;
        height: auto;
        margin: 0 auto;
    }

    .list-content {
        text-align: center;
    }

    .list-content h1 {
        font-size: 18px;
        font-weight: bold;
        margin-bottom: 10px;
    }

    .list-content p.web {
        font-size: 14px;
        margin-bottom: 5px;
    }

    .list-content p.content {
        font-size: 16px;
        line-height: 1.5;
        margin-bottom: 20px;
    }
</style>

    </head>
    <body>
        <div class="form-container">
            <h2>Crear productoras</h2>
            <div>
                <label for="txtIdn">Id:</label>
                <asp:TextBox ID="txtId" runat="server"></asp:TextBox>
            </div>
            <div>
                <label for="txtImg">Imagen:</label>
                <asp:TextBox ID="txtImg" runat="server"></asp:TextBox>
            </div>
            <div>
                <label for="txtNombre">Nombre:</label>
                <asp:TextBox ID="txtNombre" runat="server"></asp:TextBox>
            </div>
            <div>
                <label for="txtDescripcion">Descripción:</label>
                <asp:TextBox ID="txtDescripcion" runat="server" TextMode="MultiLine" Rows="4"></asp:TextBox>
            </div>
             <div>
                <label for="txtWebn">Web:</label>
                <asp:TextBox ID="txtWeb" runat="server" TextMode="MultiLine" Rows="4"></asp:TextBox>
            </div>
<div>
    <asp:Button ID="btnCrearProductora" runat="server" Text="Crear Productora" OnClick="onCrear" style="background-color: green;" />
    <asp:Button ID="btnLeerNombre" runat="server" Text="Leer Productora por nombre" OnClick="onLeerNombre" style="background-color: green;" />
    <asp:Button ID="btnLeerID" runat="server" Text="Leer Productora por ID" OnClick="onLeerId" style="background-color: green;" />
    <asp:Button ID="btnBorrarProductora" runat="server" Text="Borrar Productora" OnClick="onBorrar" style="background-color: green;" />
    <asp:Button ID="btnUpdateProductora" runat="server" Text="Actualizar Productora" OnClick="onUpdate" style="background-color: green;" />
    <asp:Button ID="btnLeerTodas" runat="server" Text="Mostrar todas las productoras" OnClick="onLeerTodas" style="background-color: green;" />
</div>

        </div>

        <div class="container bg-light overflow-auto list-container" style="min-height:599px;">
            <asp:Label ID="PResultado" runat="server" Text =" " />&nbsp;
            <br />
            <asp:ListView ID="ListView1" runat="server">
                <ItemTemplate>
                    <div class="container">
                        <div class="list">
                            <div class="list-img">
                                <img src='<%#Eval("imagen") %>' />
                            </div>
                            <div class="list-content">
                                <h1><%#Eval("nombre") %></h1>
                                <p class="web"><%#Eval("web") %></p>
                                <p class="content"><%#Eval("descripcion") %></p>
                            </div>
                        </div>
                    </div>
                </ItemTemplate>
            </asp:ListView>
        </div>
    </body>
</html>
</asp:Content>
