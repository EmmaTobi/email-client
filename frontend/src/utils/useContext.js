import React, { createContext, useState } from "react";

export const MyContext = createContext();

export const EmailConnectionProvider = ({ children }) => {

    const [connectionInfo, setConnectionInfo] = useState({
                                                        hostname : "",
                                                        serverType : "",
                                                        encryption : "",
                                                        port : "",
                                                        username : "",
                                                        password : ""
                                                        });
    const [connected, setConnected] = useState(false);


  return (
    <MyContext.Provider
      value={{
        connectionInfo,
        setConnectionInfo,
        connected,
        setConnected
      }}
    >
      {children}
    </MyContext.Provider>
  );
};