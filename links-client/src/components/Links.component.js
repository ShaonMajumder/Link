import React, { useState } from 'react';
import 'bootstrap/dist/css/bootstrap.min.css';
import '../resources/app.css';
import Swal from 'sweetalert2';
import Table from 'react-bootstrap/Table';
import PaginationCustom from './Pagination';
import { useLinksQuery } from '../services/api';
import { Link } from 'react-router-dom'
import Button from 'react-bootstrap/Button';
import { useDeleteLinkMutation } from '../services/api';
import { useHistory } from 'react-router-dom';
import store from '../store'; //important without it listner in extra reducer doesn't work
import { GoTrashcan } from 'react-icons/go';
import { FaEdit } from 'react-icons/fa'

const LinkList = (props) => {
    console.log('props.loggedIn ', props.loggedIn)
    const page = props.page;
    const setPage = props.setPage;
    const linkItemsAll = props.linkItems;
    const setLinkItemsAll = props.setLinkItems;
    console.log(props)
    const history = useHistory();
    const [deleteLink, { isLoading3 }] = useDeleteLinkMutation({
        fixedCacheKey: 'shared-update-post',
    })
    const [validationError, setValidationError] = useState({})



    const deleteProduct = async (id) => {

        const isConfirm = await Swal.fire({
            title: 'Are you sure?',
            text: "You won't be able to revert this!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            return result.isConfirmed
        });

        if (!isConfirm) {
            return;
        }



        deleteLink(id)
            .unwrap()
            .then((response) => {

                // setLinkItemsAll(linkItemsAll.filter(link => link.id !== id))
                Swal.fire({
                    icon: "success",
                    text: response.data.message
                })
                history.push('/')
            })
            .catch((error) => {
                let errors = Object.entries(error.data.errors).map(([key, value]) => (
                    value
                ))
                Swal.fire({
                    text: errors,
                    icon: "error"
                })
            })

        // }).catch(({response})=>{
        //   if(response.status===422){
        //     setValidationError(response.data.errors)
        //   }
        // })

    }

    //run createApi query, set data from reducer listner, then access data into component from store
    const { data: linkItems, isLoading, isSuccess, isError } = useLinksQuery(page, { skip: !props.loggedIn })
    console.log(' index', linkItems)

    React.useEffect(() => {
        if (linkItems) {
            setLinkItemsAll(linkItems.data.links.data)
        }
    }, [linkItems])

    if (props.loggedIn && linkItems) {
        let data = linkItems.data.links
        var data_prop = [data.current_page, data.last_page, isSuccess, setPage];
        const linkList = linkItemsAll.map(({ id, link, tag_label }) =>
            < tr key={id} >
                <td>{id}</td>
                <td>{link}</td>
                <td>{Object.values(tag_label || []).join(', ')}</td>
                <td>
                    <GoTrashcan className='table-icons' onClick={() => deleteProduct(id)} />
                    <FaEdit className='table-icons' onClick={() => history.push(`/links/update/${id}`)} />
                </td>
            </tr >
        );

        return (
            <div className="list-group">
                <Link className='btn btn-primary mb-2 float-end primary-background' to={"/links/create"}>
                    Create Link
                </Link>
                <Table responsive="sm" striped bordered hover >
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Link</th>
                            <th>Tags</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        {linkList}
                    </tbody>
                </Table>


                <PaginationCustom props={data_prop} ></PaginationCustom>
            </div>
        );
    }
    return (
        <div className="alert alert-warning">You are not logged in.</div>
    );
};

export default LinkList;
