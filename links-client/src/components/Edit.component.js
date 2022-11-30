import React, { useEffect, useState } from "react";
import Form from 'react-bootstrap/Form'
import Button from 'react-bootstrap/Button';
import Row from 'react-bootstrap/Row';
import Col from 'react-bootstrap/Col';
import { Redirect, useParams, useForm } from 'react-router-dom'
import axios from 'axios';
import Swal from 'sweetalert2';
import apiClient, { linksApi, useGetLinkQuery, useGetTagsQuery, link_create_url, get_link_url, useUpdateLinkMutation } from '../services/api';
import store from "../store";
import { setPageItem } from "../reducers/linkSlice";
import { useDispatch } from "react-redux";
import CreatableSelect from 'react-select/creatable';

const API_URL = process.env.REACT_APP_API_URL || "http://localhost:8000"


export default function EditLink(props) {
  const { refetch } = linksApi.endpoints.links.useQuerySubscription(props.page)
  const dispatch = useDispatch();
  const [updateLink, { isLoading2 }] = useUpdateLinkMutation()
  const { id } = useParams()
  const history = props.history()
  const linkItemsAll = props.linkItems
  const setLinkItemsAll = props.setLinkItems
  // console.log('edit id ',useParams(),props.history())
  // console.log('success edit',store.getState().links.linkItems)

  const [link, setLink] = useState("")
  const [tags, setTags] = useState({value:"",label:""})
  const [atags, setAtags] = useState({value:"",label:""})
  const [selectedTags, setSelectedTags] = useState([])
  const [author, setAuthor] = useState("")
  const [description, setDescription] = useState("")
  const [amount, setAmount] = useState("")
  const [image, setImage] = useState(null)
  const [validationError,setValidationError] = useState({})

  // const { handleSubmit, control, errors, setValue } = useForm();

  let { linkid } = useParams();
  const { data: linkItem, isLoading, isSuccess, isError }  = useGetLinkQuery(linkid);
  const { data: tagItems, isLoadingTag, isSuccessTag, isErrorTag }  = useGetTagsQuery();
  React.useEffect(() => {
    if (tagItems){
      setTags( tagItems.data.map(({name,causer_id})=>{
        // console.log(name)
        return { value: name, label: name }
      }) )
    }

    if (linkItem){
      // console.log(linkItem.data)
      setLink(linkItem.data.link)
      setDescription(linkItem.data.description)
      setSelectedTags(linkItem.data.tags)
      // setTags(linkItem.data.tag_label)
      // console.log(linkItem.data.tags)
      if(linkItem.data.tag_label){

        let labels = Object.entries(linkItem.data.tag_label);
        
        setAtags(labels.map( name => {
          return { value: name[1], label: name[1] }
        }))
        console.log(atags)
      }
      
      // console.log(linkItem.data.tag_label)

      

      // setAvaiableTags( linkItem.data.tag_label.map(({name,id})=>{
      //   console.log(name)
      //   return { value: id, label: name }
      // }) )
      
      // setTags( tagItems.data.map(({name,causer_id})=>{
      //   console.log(name)
      //   return { value: name, label: name }
      // }) )
    }
  },[linkItem,tagItems])

  // useEffect(()=>{
  //   fetchProduct()
  // }, [])

  // const fetchProduct = async () => {
    
  //   apiClient.get(`${get_link_url}/${id}`).then(({data})=>{
  //     const { title, author } = data.data
  //     setTitle(title)
  //     setAuthor(author)
  //   }).catch(({response:{data}})=>{
  //     Swal.fire({
  //       text:data.message,
  //       icon:"error"
  //     })
  //   })
  // }

  const changeHandler = (event) => {
		setImage(event.target.files[0]);
	};

  const updateProduct = async (e) => {
    e.preventDefault();

    const formData = new FormData()
    formData.append('_method', 'PATCH');
    formData.append('link', link)
    formData.append('description', description)
    if(image!==null){
      formData.append('image', image)
    }

    const json_data = {
      'id' : linkid,
      'link' : link,
      'description' : description,
      'tags': selectedTags
    }
    updateLink(json_data).unwrap()
    .then((payload) => {
      
      Swal.fire({
        icon:"success",
        text: payload.data.message
      })
         
      // setLinkItemsAll(  linkItemsAll.map((item, index) => {  if( item.id == id){ return payload.data.data }else{ return item } })  )
      props.setPage(props.page)
      // refetch()
      history.push('/')
    })
    .catch((response)=>{
      console.log('rejected', response)
      if(response.status===422){
        setValidationError(response.data.errors)
      }else{
        Swal.fire({
          text:response.data.message,
          icon:"error"
        })
      }
    })
  }

  return (
    <div className="container">
      <div className="row justify-content-center">
        <div className="col-12 col-sm-12 col-md-6">
          <div className="card">
            <div className="card-body">
              <h4 className="card-title">Update Link</h4>
              <hr />
              <div className="form-wrapper">
                {
                  Object.keys(validationError).length > 0 && (
                    <div className="row">
                      <div className="col-12">
                        <div className="alert alert-danger">
                          <ul className="mb-0">
                            {
                              Object.entries(validationError).map(([key, value])=>(
                                <li key={key}>{value}</li>   
                              ))
                            }
                          </ul>
                        </div>
                      </div>
                    </div>
                  )
                }
                <Form onSubmit={updateProduct}>
                  <Row> 
                      <Col>
                        <Form.Group controlId="Name">
                            <Form.Label>link</Form.Label>
                            <Form.Control type="text" value={link} onChange={(event)=>{
                              setLink(event.target.value)
                            }}/>
                        </Form.Group>
                      </Col>  
                  </Row>
                  <Row className="my-3">
                      <Col>
                        <Form.Group controlId="Tag">
                            <Form.Label>Tags</Form.Label>
                            {/* <Form.Control as="textarea" rows={3} value={author} onChange={(event)=>{
                              setAuthor(event.target.value)
                            }}/> */}
                            <CreatableSelect 
                              // isClearable
                              value={atags}
                              options={tags} 
                              isMulti 
                              onChange={(choice) => {
                                let llal = choice.map(({label,value}) => {
                                  return value;
                                })
                                setSelectedTags( llal )
                                setAtags( choice.map(({label,value}) => {
                                  return { value: id, label: label }
                                }) )
                              }} 
                            />
                        </Form.Group>
                      </Col>
                  </Row>
                  {/* <Row> 
                      <Col>
                        <Form.Group controlId="Author">
                            <Form.Label>Author</Form.Label>
                            <Form.Control type="text" value={author} onChange={(event)=>{
                              setAuthor(event.target.value)
                            }}/>
                        </Form.Group>
                      </Col>  
                  </Row> */}
                  <Row className="my-3">
                      <Col>
                        <Form.Group controlId="Description">
                            <Form.Label>Description</Form.Label>
                            <Form.Control as="textarea" rows={3} value={description} onChange={(event)=>{
                              setDescription(event.target.value)
                            }}/>
                        </Form.Group>
                      </Col>
                  </Row>
                  {/* <Row> 
                      <Col>
                        <Form.Group controlId="Name">
                            <Form.Label>Amount</Form.Label>
                            <Form.Control type="text" value={amount} onChange={(event)=>{
                              setAmount(event.target.value)
                            }}/>
                        </Form.Group>
                      </Col>  
                  </Row>
                  <Row>
                    <Col>
                      <Form.Group controlId="Image" className="mb-3">
                        <Form.Label>Image</Form.Label>
                        <Form.Control type="file" onChange={changeHandler} />
                      </Form.Group>
                    </Col>
                  </Row> */}
                  <Button variant="primary" className="mt-2" size="lg" block="block" type="submit">
                    Update
                  </Button>
                </Form>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  )
}