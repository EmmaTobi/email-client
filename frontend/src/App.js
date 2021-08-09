
import FormComponent from './components/FormComponent';
import MailMessageComponent from './components/MailMessageComponent';
import HeadersListComponent from './components/HeadersListComponent';
import { EmailConnectionProvider } from './utils/useContext';
import 'bootstrap/dist/css/bootstrap.min.css';
import './App.css';

const App = () => {
  return (
    <div className="container wrapper">
      <div className={"row"}>
        <EmailConnectionProvider>
          <FormComponent />
          <HeadersListComponent></HeadersListComponent>
        </EmailConnectionProvider>
      </div>
      <MailMessageComponent />
    </div>
  );
};

export default App;
