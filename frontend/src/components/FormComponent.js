import React, {Component} from 'react';
import Spinner from 'react-bootstrap/Spinner';
import EmailClientService from "../services/EmailClientService";
import { MyContext } from "../utils/useContext";
import eventBus from "../utils/eventBus";
import "../css/Form.css";

export default class FormComponent extends Component{

  static contextType = MyContext

  constructor(props){

    super(props)
    this.state = {
          connectionData : {},
          connected : false,
          loading : false
    };
    
    this.handleChange = this.handleChange.bind(this);
    this.handleSubmit = this.handleSubmit.bind(this);
    this.dispatchDataOnConnectionUpdate = this.dispatchDataOnConnectionUpdate.bind(this);
    this.resetState = this.resetState.bind(this);
  }

  handleChange(event) {
    let data = {...this.state.connectionData}
    data[event.target.name] = event.target.value
    this.setState({ connectionData : data});
  }

  resetState(){
    window.location.reload();
  }

  handleSubmit(event) {
    if(!this.state.connected){
      this.setState({loading : true})
      let {setConnectionInfo} = this.context;
      setConnectionInfo(this.state.connectionData);
      let connectionInfo = this.state.connectionData
      let {hostname, serverType, encryption, port, username, password} = connectionInfo
      EmailClientService.connect(hostname, serverType, port, encryption, username, password)
                        .then(response => {
                          alert(response.data.message)
                          this.setState({connected: true, loading : false})
                          this.dispatchDataOnConnectionUpdate(response.data.data.msgCount)
                        })
                        .catch((err) =>{
                          this.setState({loading : false})
                          alert(err.response ? err.response.data.message : err)
                        } )
    }else{
      this.resetState()
    }
    event.preventDefault();
  }

  dispatchDataOnConnectionUpdate(msgCount){
    eventBus.dispatch("connectionSuccessful", { msgCount,connectionInfo : this.state.connectionData});
  }

  render(){
      return (
        <form action='#' method="POST"  >
          <fieldset className="fieldSet" >
          <div className="formGroup">
            <label htmlFor='server-type'>Server type</label>
            <select className="select" disabled={this.state.connected}  name='serverType' onChange={this.handleChange} required id='serverType'>
              <option value=''>Select Server Type</option>
              <option value='imap'>Imap</option>
              <option value='pop3'>Pop3</option>
            </select>
          </div>
          <div className="formGroup">
            <label htmlFor='encryption'>Encryption</label>
            <select className="select" disabled={this.state.connected} name='encryption' required   onChange={this.handleChange} id='encryption'>
              <option value=''>Select Encryption Type</option>
              <option value='ssl'>SSL/TLS</option>
              <option value='starttls'>STARTTLS</option>
              <option value='notls'>NOTLS</option>
            </select>
          </div>
          <div className="formGroup">
            <label htmlFor='server'>Server</label>
            <input className="input" disabled={this.state.connected} type='text'  onChange={this.handleChange} required name='hostname' />
          </div>
          <div className="formGroup">
            <label htmlFor='username'>Username</label>
            <input className="input" disabled={this.state.connected}  type='text' required  onChange={this.handleChange} name='username' />
          </div>
          <div className="formGroup">
            <label htmlFor='port'>Port</label>
            <input className="input" disabled={this.state.connected} type='number' required  onChange={this.handleChange} name='port' />
          </div>
    
          <div className="formGroup">
            <label htmlFor='password'>Password</label>
            <input className="input"  disabled={this.state.connected} type='password' required onChange={this.handleChange} name='password' />
          </div>

          <button className="button" onClick={this.handleSubmit} type='submit' disabled={false}>{ !this.state.connected ? "Connect" : "Disconnect"}
                &nbsp; <Spinner style={{display : this.state.loading ? 'inline-block' : 'none'}} animation="border" as="span" variant="secondary" />
          </button>
          </fieldset>
        </form>
      );
  }

};

