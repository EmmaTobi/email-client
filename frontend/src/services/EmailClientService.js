import axios from "axios";

const BASE_URL = "http://localhost:8000/api/"
const CONNECTION_URL = BASE_URL + "connect";
const GET_HEADERS_URL = BASE_URL + "get-headers";
const GET_INBOX_URL = BASE_URL + "get-inbox";

 class EmailClientService {

    retrieveHeaders(connectionData, start, end){
        return axios.post(`${GET_HEADERS_URL}`, {...connectionData, start, end});
    }

    retrieveInbox(connectionData, msgId){
        return axios.post(`${GET_INBOX_URL}`, {...connectionData, msgId});
    }

    connect(hostname, serverType, port, encryption, username, password ){
        return axios.post(`${CONNECTION_URL}`, {hostname, serverType, port, encryption, username, password});
    }

}

export default new EmailClientService();