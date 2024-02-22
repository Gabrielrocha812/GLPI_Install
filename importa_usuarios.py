####################################################################
# Gabriel Rocha
# Procedimento: Importação de usuários para GLPI
# @Responsável: grgsouza1@gmail.com
# @Data: 21/02/2024
# @Versão: 1.0
# @Homologado: GLPI 10.0.6
####################################################################

import csv
import requests

# Configurações do GLPI

glpi_url = "http://10.2.8.95/apirest.php"
glpi_app_token = "lNbhyngyx7otGdednFCpICrPHXn84uFHQRCid9uL"

#para obter o glpi_user_token, devemos fazer o seguinte:
#1 - http://10.2.8.95/apirest.php/initSession?user_token=l6olHQZAaVLd3zjHxC1GJxzCcRPCHrVHMQWFZ4I2&app_token=lNbhyngyx7otGdednFCpICrPHXn84uFHQRCid9uL
#2 -acessar o link acima, em user_token, vamos pegar nosso token de usuario que fica em Administração>Usuários>Seleciona o usuario e uma das ultimas 
#opções vamos ter Chaves de acesso remoto. Vamos gerar um API TOKEN e vamos colocar em user_token.
#3- logo vamos copiar nosso glpi_app_token e colar tbm. Ao acessar o link, vamos ter nossa glpi_user_token valida

glpi_user_token = "ca5u4ssj06o4a9dg3dbq2l1svc"

# Endpoint de criação de usuários no GLPI
user_endpoint = "/User"
email_endpoint = "/UserEmail"

def create_user(username, password, last_name, first_name, email, default_profile):
    headers = {
        "Content-Type": "application/json",
        "App-Token": glpi_app_token,
        "Session-Token": glpi_user_token,
    }

    user_data = {
        "input": {
            "name": username,
            "password": password,
            "firstname": first_name,
            "realname":  last_name,
            "email": email,
            "entities_id": 2,
            "subentities_id": 1,
            "profiles_id": default_profile,
        }
    }

    response = requests.post(glpi_url + user_endpoint, json=user_data, headers=headers)

    if response.status_code == 201:
        user_id = response.json()["id"]
        print(f"Usuário {username} criado com sucesso. ID: {user_id}")
        return user_id
    else:
        print(f"Falha ao criar usuário {username}. Código de status: {response.status_code}")
        print(response.text)
        return None

def create_email(user_id, email):
    headers = {
        "Content-Type": "application/json",
        "App-Token": glpi_app_token,
        "Session-Token": glpi_user_token,
    }

    email_data = {
        "input": {
            "users_id": user_id,
            "email": email,
            "is_default": 1,  
        }
    }

    response = requests.post(glpi_url + email_endpoint, json=email_data, headers=headers)

    if response.status_code == 201:
        print(f"E-mail {email} associado ao usuário ID {user_id} com sucesso.")
    else:
        print(f"Falha ao associar e-mail ao usuário ID {user_id}. Código de status: {response.status_code}")
        print(response.text)

def import_users_from_csv(csv_file_path):
    with open(csv_file_path, newline="", encoding="ISO-8859-1") as csvfile:
        reader = csv.DictReader(csvfile)
        for row in reader:
            username = row.get("Username", "")
            password = row.get("Password", "")
            email = row.get("Email", "")
            last_name = row.get("LastName", "")  
            first_name = row.get("FirstName", "")  
            default_profile = int(row.get("DefaultProfile", 1))

            print(f"first_name: {first_name}, last_name: {last_name}")

            user_id = create_user(username, password, last_name, first_name, email, default_profile)

            if user_id:
                create_email(user_id, email)

# Caminho do seu arquivo .csv
#Seu arquivo .csv tem que estar nesse formato:
#Username,Password,Email,LastName,FirstName,DefaultProfile
#abarbosa,123,abarbosa@brandt.com.br,Barbosa - Brandt,André,1

import_users_from_csv("C:/Users/via3gr/Documents/arquivo.csv")
