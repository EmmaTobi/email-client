import React, {Component} from 'react';
import EmailClientService from "../services/EmailClientService";
import PaginationComponent from "./PaginationComponent";
import { MyContext } from "../utils/useContext";
import eventBus from "../utils/eventBus";
import "../css/HeadersList.css";


export default class HeadersListComponent extends Component{

  static contextType = MyContext
  
  constructor(props){
      super(props)
      this.state = {
        headers : [],
        connectionInfo: {},
        totalMsgCount : 0,
        start : 1,
        end : 10
      }
      this.getHeaders = this.getHeaders.bind(this);
      this.resolveEndBoundary = this.resolveEndBoundary.bind(this);
  }

  componentDidMount(){
        eventBus.on("connectionSuccessful", (data) => {
            this.setState({totalMsgCount: data.msgCount, connectionInfo : data.connectionInfo})
            this.getHeaders(1, this.resolveEndBoundary(10))
        })
        eventBus.on("pageChanged", (data) => {
            this.setState( {start : data.start, end : data.end})
            this.getHeaders(data.start, this.resolveEndBoundary(data.end))
        })
  }
  /**
   * Determine  boundary when getting paginated headers
   * @param {*} end 
   * @return int
   */
  resolveEndBoundary(end){
    return end > this.state.totalMsgCount ? this.state.totalMsgCount : end;
  }

  /**
   * Get Headers from mailbox
   * @param {*} end 
   * @return int
   */
  getHeaders(start, end){
        EmailClientService.retrieveHeaders(this.state.connectionInfo, start, end )
        .then(response => {
          this.setState({
            headers : response.data.data
          });
        })
        .catch(err => {return alert("Network Error Please Try Again..");});
  }

  render(){
      let count = this.state.headers.length;
      if(count > 0){
        return (
          <div className="wrapper">
              <PaginationComponent
                data={this.state.headers}
                totalMsgCount={this.state.totalMsgCount}
                connectionInfo={this.state.connectionInfo}
                pageLimit={5}
                dataLimit={5} />
          </div>
        );
      }else{
        return (
          <div className="wrapper">
          </div>
        )
      }
  }

};

