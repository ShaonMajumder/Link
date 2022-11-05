import React from 'react';
import apiClient,{link_url} from '../services/api';
import Cookies from 'js-cookie';
import { getLinkItems } from '../reducers/linkSlice';
import { useDispatch, useSelector } from "react-redux";

const Links = (props) => {
    const dispatch = useDispatch();
    const { linkItems, isLoading } = useSelector((store) => store.links);
    // const [links, setLinks] = React.useState([]);
    React.useEffect(() => {
        if (props.loggedIn) {
            dispatch(getLinkItems());
        }
      
    }, []);
    console.log(linkItems)
    const linkList = linkItems.map((link) => 
        <div key={link.id}
            className="list-group-item"
        >
            <h5>{link.title}</h5>
            <small>{link.author}</small>
        </div>
    );
    if (props.loggedIn) {
        return (
            <div className="list-group">{linkList}</div>
        );
    }
    return (
        <div className="alert alert-warning">You are not logged in.</div>
    );
};

export default Links;
