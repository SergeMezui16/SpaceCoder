import './styles/app.css';
import React from 'react';
import ReactDOM from 'react-dom/client';

// Web Components Imports
import './js/index';

class App extends React.Component
{

    constructor () {
        super()

    }

    render (){

        return (
        <>
        </>
        )
    }
}

const root = document.getElementById('react-root')
if(root !== null){
    console.log(root)
    ReactDOM.createRoot(root).render(
        <App/>
    ) 
}