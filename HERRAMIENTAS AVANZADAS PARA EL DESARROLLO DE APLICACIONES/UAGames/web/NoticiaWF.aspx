<%@ Page Title="" Language="C#" MasterPageFile="~/Site1.Master" AutoEventWireup="true" CodeBehind="NoticiaWF.aspx.cs" Inherits="web.NoticiaWF" %>
<asp:Content ID="Content1" ContentPlaceHolderID="main" runat="server">
    <div class="container bg-light overflow-auto list-container" style="min-height:300px;">
      <asp:ListView ID="ListView1" runat="server" OnItemDataBound="ListView1_ItemDataBound">
    <ItemTemplate>
        <div class="list" runat="server" id="divItem" style="margin-bottom: 10px;">
            <div class="list-img" style="height: 100%;">
                <img src='<%#Eval("imagen") %>' alt="Imagen de la noticia" style="width: 150px; height: 100%; object-fit: cover;" />
            </div>
            <div class="list-content" style="position: absolute; top: 0; left: 150px; padding: 20px; background-color: rgba(255, 255, 255, 0.8); text-align: left;">
                <h1 style="font-size: 22px; font-weight: 700; margin-bottom: 10px; color: #333;"><%#Eval("titulo") %></h1>
                <p class="list-date" style="font-size: 14px; margin-bottom: 10px; color: #888;"><%#Eval("fecha_public") %></p>
                <p class="list-author" style="font-size: 14px; margin-bottom: 10px; color: #888;">Autor: <%#Eval("nombre") %></p>
                <a href='<%#"NoticiaCompleta.aspx?id=" + Eval("id") %>' class="noticia-enlace" >Leer más</a>
            </div>
        </div>
        
    </ItemTemplate>
         
</asp:ListView> </div>
   <style>
    .list-container {
        display: flex;
        flex-wrap: wrap;
        justify-content: flex-start;
        margin-bottom: 0px;
    }

    .list {
        width: calc(50% - 20px);
        height: 175px;
        margin-bottom: 10px;
        border: 1px solid #ddd;
        border-radius: 8px;
        overflow: hidden;
        background-color: #fff;
        box-shadow: 0px 3px 6px rgba(0, 0, 0, 0.1);
        display: flex;
        flex-direction: row;
        align-items: center;
        position: relative;
    }

    .list-img {
        width: 150px;
        height: 50%;
        background-color: #f5f5f5;
    }

    .list-img img {
        width: 100%;
        height: 50%;
        object-fit: cover;
    }

    .list-content {
        position: absolute;
        top: 0;
        left: 150px;
        padding: 20px;
        background-color: rgba(255, 255, 255, 0.8);
        text-align: left;
    }

    .list-content h1 {
        font-size: 22px;
        font-weight: 700;
        margin-bottom: 10px;
        color: #333;
    }

    .list-date {
        font-size: 14px;
        margin-bottom: 10px;
        color: #888;
    }

    .list-author {
        font-size: 14px;
        margin-bottom: 10px;
        color: #888;
    }

    .list-button {
        position: absolute;
        bottom: 10px;
        right: 10px;
    }

    .noticia-enlace {
        display: inline-block;
        padding: 8px 16px;
        background-color: #0080FF;
        color: #fff;
        text-decoration: none;
        border-radius: 4px;
        transition: background-color 0.3s;
    }

    .noticia-enlace:hover {
        background-color: #0065CC;
    }
</style>

</asp:Content>
