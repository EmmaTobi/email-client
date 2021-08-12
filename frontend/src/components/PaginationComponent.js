import React, {useState} from 'react';
import eventBus from "../utils/eventBus";
import Table from 'react-bootstrap/Table';
import 'bootstrap/dist/css/bootstrap.min.css';
import "../css/Pagination.css";

function PaginationComponent({ data, totalMsgCount, connectionInfo,  pageLimit, dataLimit }) {

  const possibleNumberOfPages = Math.round(totalMsgCount / pageLimit)
  const displayPaginationState = possibleNumberOfPages < pageLimit;
  const [pages] = useState(possibleNumberOfPages);
  const [currentPage, setCurrentPage] = useState(1);

  pageLimit = displayPaginationState ? possibleNumberOfPages : pageLimit

  function goToNextPage() {
      setCurrentPage((page) => handlePageChange(page, 1));
  }

  function goToPreviousPage() {
      setCurrentPage((page) => handlePageChange(page, -1));
  }

  function changePage(event) {
   const pageNumber = Number(event.target.textContent);
   setCurrentPage(pageNumber);
   handlePageChange(pageNumber, 0);
  }

  const getPaginatedData = () => {
      return data;
  };

  const handlePageChange = (page, delta) => {
    const current = page + delta
    const start = current * dataLimit - dataLimit ;
    const end = start + dataLimit;
    eventBus.dispatch("pageChanged",  {start : start + 1, end});
    return currentPage;
  }

  const getPaginationGroup = () => {
    let start = Math.floor((currentPage - 1) / pageLimit) * pageLimit;
    return new Array(pageLimit).fill().map((_, idx) => start + idx + 1);
  };

  const handleHeaderClicked = (headerId) => {
    eventBus.dispatch("getInbox", { msgId : headerId, connectionInfo });
  }

  return (
   
   <div>

    {/* show the posts, 10 posts at a time */}
    <div className="dataContainer">
       { <Table  striped responsive borderded="true" hover  variant="dark" >
            <tbody>
              <tr>
                <th>S/N</th>
                <th>From</th>
                <th style={{minWidth : "170"}}>Subject</th>
                <th>Date</th>
              </tr>
              
              {
                getPaginatedData().map((header, index) => 
                    <tr className="cursor" key={header.id} onClick={  () => handleHeaderClicked(header.id)}>
                        <td>{header.id}</td>
                        <td>{header.fromAddress}</td>
                        <td>{header.subject}</td>
                        <td>{header.date}</td>
                    </tr>
                )
              }
            </tbody>
          </Table> 
         }
         
    </div>

    {/* show the pagination
        it consists of next and previous buttons
        along with page numbers, in our case, 5 page
        numbers at a time
    */}
    <div className="pagination" style={{display : !displayPaginationState ? 'block': 'none'}}>
      {/* previous button */}
      <button 
        onClick={goToPreviousPage}
        className={`prev ${currentPage === 1 ? 'disabled' : ''}`}
      >
        prev
      </button>

      {/* show page numbers */}
      {getPaginationGroup().map((item, index) => (
        <button
          key={index}
          onClick={changePage}
          className={`paginationItem ${currentPage === item ? 'active' : null}`}
        >
          <span>{item}</span>
        </button>
      ))}

      {/* next button */}
      <button 
        onClick={goToNextPage}
        className={`next ${currentPage === pages ? 'disabled' : ''}`}
      >
        next
      </button>
    </div>
  </div>
   
  );
}

export default PaginationComponent;