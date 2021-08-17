import axios from "axios";

const BASE_URL = "http://localhost:8000/api/"
const CONNECTION_URL = BASE_URL + "connect";
const GET_HEADERS_URL = BASE_URL + "get-headers";
const GET_INBOX_URL = BASE_URL + "get-inbox";

/**
 * Service To Handle Email Backend Calls
 */
 class EmailClientService {

    /**
     * 
     * @param {json} connectionData the email client auth payload
     * @param {int} start int pagination start boundary
     * @param {int} end int pagination end boundary
     */
    retrieveHeaders(connectionData, start, end){
        return axios.post(`${GET_HEADERS_URL}`, {...connectionData, start, end});
    }

    /**
     * 
     * @param {json} connectionData the email client auth payload
     * @param {int} msgId int the inbox id
     */
    retrieveInbox(connectionData, msgId){
        return axios.post(`${GET_INBOX_URL}`, {...connectionData, msgId});
    }

    /**
     * 
     * @param {string} hostname  the hostname. example mail.google.com
     * @param {string} serverType  pagination start boundary
     * @param {int} port int the server port number 
     * @param {string} encryption  the desired connection encryption mode eg SSL/TLS
     * @param {string} username  the email username of the client
     * @param {string} password  the email password  of the client
     */
    connect(hostname, serverType, port, encryption, username, password ){
        return axios.post(`${CONNECTION_URL}`, {hostname, serverType, port, encryption, username, password});
    }

}

export default new EmailClientService();