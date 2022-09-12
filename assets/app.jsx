import './styles/app.scss';
import React from 'react';
import ReactDOM from 'react-dom/client';


class App extends React.Component
{
    render (){
        return (
            <>
                <header className="header">
                     <div className="logo">
                        <img src="" alt="" />
                     </div>
                </header>
            </>
            
        )
    }
}


ReactDOM.createRoot(document.getElementById('react-root')).render(
    <App/>
);