export async function GET() {
    return Response.json({
        'success': true,
        'message': 'Data retrieved successfully',
        'data': {
            current_time: (new Date()).toISOString()
        }
    })
}