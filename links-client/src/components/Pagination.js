import React from 'react';
import 'bootstrap/dist/css/bootstrap.min.css';
import Pagination from 'react-bootstrap/Pagination';
import '../resources/app.css';

function PaginationCustom (props)  {
    var [ current_page, last_page, isSuccess, setPage] = props.props
    
    return (    
        <Pagination>
            <Pagination.First 
                style={{display: current_page !== 1 ? 'block' : 'none' }} 
                onClick={
                    () => {
                            if(current_page !== 1){
                                setPage(1)
                            }
                    }
                }
                isLoading={isSuccess}
            />

            

            <Pagination.Prev 
                style={{display: current_page !== 1 ? 'block' : 'none' }}
                onClick={
                    () => { 
                        if(current_page > 1){
                            setPage(current_page - 1)
                        }
                    }
                }
                isLoading={isSuccess}
            />

            <Pagination.Item 
                style={{display: (current_page !== 1) ? 'block' : 'none' }} 
                onClick={
                    () => { 
                        if(current_page !== 1){
                            setPage(1)
                        }
                    }
                }
                isLoading={isSuccess}
            >{1}</Pagination.Item>

            <Pagination.Ellipsis 
                style={{display: (last_page > 3 && ( current_page !== last_page && current_page !== 1 ) ) ? 'block' : 'none' }}
            />
    
            <Pagination.Item 
                isLoading={isSuccess}
            active>{current_page}</Pagination.Item>

            <Pagination.Ellipsis 
                style={{display: (last_page > 3 && ( current_page !== last_page && current_page !== 1 ) ) ? 'block' : 'none' }}
            /> 

            <Pagination.Item
                style={{display: (last_page > 1 && current_page !== last_page ) ? 'block' : 'none' }}
                onClick={
                    () => { 
                        if(current_page !== last_page){
                            setPage(last_page)
                        }
                    }
                }
                isLoading={isSuccess}
            >{last_page}</Pagination.Item>

            <Pagination.Next 
                style={{display: current_page < last_page ? 'block' : 'none' }} 
                onClick={
                    () => {
                        if(current_page < last_page){
                            setPage(current_page + 1)
                        }
                    }
                }
                isLoading={isSuccess}
            />
            
            <Pagination.Last 
                style={{display: current_page !== last_page ? 'block' : 'none' }} 
                onClick={
                    () => {
                        if(current_page !== last_page){
                            setPage(last_page)
                        }
                    }
                }
                isLoading={isSuccess}
            />
        </Pagination>
    );
    
};

export default PaginationCustom;
