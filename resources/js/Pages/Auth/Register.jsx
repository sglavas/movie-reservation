import Layout from "../Components/Layout"
import SuccessMessage from "../Components/SuccessMessage"
import { usePage } from "@inertiajs/react"
import { useForm } from '@inertiajs/react';
import { Link } from '@inertiajs/react'
import { useEffect, useState } from "react"
import Button from "../Components/Button"
import FormInput from "../Components/FormInput";

export default function Register() {
    const { errors } = usePage().props;
    const [ isVisible, setIsVisible ] = useState(false);

    const { post, data, setData, reset, processing } = useForm({
        first_name: '',
        last_name: '',
        email: '',
        password: '',
        password_confirmation: '',
    })

    const submit = (e) => {
        e.preventDefault()
        post('/register', {
            preserveScroll: true,
            onSuccess: (message) => {
                reset();
            },
        });
    }
    return(
        <div>
            <form onSubmit={submit} action="/register">
                <div className="flex flex-col mt- gap-x-6 gap-y-8 sm:grid-cols-6">

                    <FormInput 
                        field='first_name'
                        type='text'
                        slot='First Name'
                        setData={setData}
                        value={data.first_name}
                    />
                    <div className='text-red-500 text-xs mt-1'>{errors['first_name'] ? errors['first_name'] : ''}</div>

                    <FormInput 
                        field='last_name'
                        type='text'
                        slot='Last Name'
                        setData={setData}
                        value={data.last_name}
                    />
                    <div className='text-red-500 text-xs mt-1'>{errors['last_name'] ? errors['last_name'] : ''}</div>

                    <FormInput 
                        field='email'
                        type='email'
                        slot='Email'
                        setData={setData}
                        value={data.email}
                    />
                    <div className='text-red-500 text-xs mt-1'>{errors.email ? errors.email : ''}</div>

                    <FormInput 
                        field='password'
                        type='password'
                        slot='Password'
                        setData={setData}
                        value={data.password}
                    />    
                    <FormInput 
                        field='password_confirmation'
                        type='password'
                        slot='Confirm Password'
                        setData={setData}
                        value={data.password_confirmation}
                    />    
                    <div className='text-red-500 text-xs mt-1'>{errors.password ? errors.password : ''}</div>


                </div> 
                <div className="mt-6 flex items-center justify-end gap-x-6">
                    <Button as={Link}
                            color='red'
                            type='button'
                            href='/'
                    >
                        Cancel
                    </Button>
                    <Button as='button'
                            color='indigo'
                            type='submit'
                            disabled={processing}
                    >
                        Register
                    </Button>
                </div>
            </form>
        </div>
    )
}

Register.layout = page => <Layout children={page} slot="Register" />