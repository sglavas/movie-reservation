import Layout from "../Components/Layout"
import SuccessMessage from "../Components/SuccessMessage"
import { usePage } from "@inertiajs/react"
import { useForm } from '@inertiajs/react';
import { Link } from '@inertiajs/react'
import { useEffect, useState } from "react"
import Button from "../Components/Button"
import FormInput from "../Components/FormInput";

export default function Login() {
    const { errors } = usePage().props;
    const [ isVisible, setIsVisible ] = useState(false);

    // Implement Inertia useForm hook
    const { post, data, setData, reset, processing } = useForm({
        email: '',
        password: ''
    })

    const submit = (e) => {
        e.preventDefault()
        post('/login', {
            preserveScroll: true,
            onSuccess: (message) => {
                reset();
            },
        });
    }

    return(
        <div>
            <form onSubmit={submit} action="/login">
                <div className="flex flex-col mt- gap-x-6 gap-y-8 sm:grid-cols-6">
                    <FormInput 
                        field='email'
                        type='email'
                        slot='Email'
                        setData={setData}
                    />
                    <div className='text-red-500 text-xs mt-1'>{errors.email ? errors.email : ''}</div>

                    <FormInput 
                        field='password'
                        type='password'
                        slot='Password'
                        setData={setData}
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
                        Login
                    </Button>
                </div>
            </form>
        </div>
    )
}

Login.layout = page => <Layout children={page} slot="Login" />