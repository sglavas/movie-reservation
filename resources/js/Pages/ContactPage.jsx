import Layout from "./Components/Layout"

export default function ContactPage() {
    return(
        <div>This is a test</div>
    )
}

ContactPage.layout = page => <Layout children={page} slot="Contact Page" />