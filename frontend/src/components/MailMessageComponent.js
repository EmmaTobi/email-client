import React, {Component} from 'react';
import EmailClientService from "../services/EmailClientService";
import eventBus from "../utils/eventBus";
import "../css/MailMessage.css";


export default class MailMessageComponent extends Component{

  constructor(props){

    super(props)
      this.state = {
          message : ""
        }
    };

    componentDidMount(){
      eventBus.on("getInbox", (data) => {
           EmailClientService.retrieveInbox(data.connectionInfo, data.msgId)
                          .then(response => {
                            eventBus.dispatch("loading", { loading : false });
                            this.setState({message : response.data.data})
                          })
                          .catch(err =>{
                             eventBus.dispatch("loading", { loading : false });
                             alert(err.response ? err.response.data.message : err) 
                            });
         }
      );
    }

  render(){
    return (
        <div style={{ height : "1000px", width: "800px", overflow: "hidden"}} className="htmlTextarea" dangerouslySetInnerHTML={ {__html: this.state.message} } > 
        </div>
    );
  }
};

